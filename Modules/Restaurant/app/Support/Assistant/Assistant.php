<?php

namespace Modules\Restaurant\Support\Assistant;

use Anthropic\Beta\Messages\BetaToolUseBlock;
use Anthropic\Client;
use Modules\Restaurant\Models\Setting;
use Modules\Restaurant\Models\User;
use Modules\Restaurant\Support\Tenancy;

/**
 * AI chat support for the admin panel, powered by Claude.
 *
 * "Trained" on the system in two ways:
 *  - resources/ai/assistant-guide.md: how every page and feature works (sent as a cached system prompt);
 *  - AssistantTools: read-only look-ups of this restaurant's live data, limited to what the person may open.
 */
class Assistant
{
    public function __construct(private User $user) {}

    public static function configured(): bool
    {
        return filled(config('restaurant.assistant.api_key'));
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history  earlier turns, plain text
     * @return string the reply (Markdown: bold, lists, links)
     */
    public function reply(string $question, array $history = []): string
    {
        $client = new Client(apiKey: config('restaurant.assistant.api_key'));
        $tools  = new AssistantTools($this->user);
        $defs   = $tools->definitions();

        $messages   = $this->history($history);
        $messages[] = ['role' => 'user', 'content' => $question];

        for ($round = 0; $round <= (int) config('restaurant.assistant.max_tool_rounds', 6); $round++) {
            $response = $client->beta->messages->create(
                maxTokens: 16000,
                messages: $messages,
                model: config('restaurant.assistant.model', 'claude-opus-5-5'),
                system: $this->system(),
                tools: $defs ?: null,
                outputConfig: ['effort' => config('restaurant.assistant.effort', 'low')],
                // If the model declines a request for policy reasons, the API retries it on a fallback model.
                fallbacks: 'default',
                betas: ['server-side-fallback-2026-07-01'],
            );

            if ($response->stopReason === 'refusal') {
                return "Sorry, I can't help with that one. Please ask me about your restaurant or how to use SajiloBhoj.";
            }

            if ($response->stopReason !== 'tool_use') {
                return $this->text($response->content) ?: 'Sorry, I could not find an answer. Please try asking another way.';
            }

            $results = [];
            foreach ($response->content as $block) {
                if (! $block instanceof BetaToolUseBlock) {
                    continue;
                }
                try {
                    $results[] = ['type' => 'tool_result', 'toolUseID' => $block->id, 'content' => $tools->run($block->name, (array) $block->input)];
                } catch (\Throwable $e) {
                    report($e);
                    $results[] = ['type' => 'tool_result', 'toolUseID' => $block->id, 'content' => 'This look-up failed: ' . class_basename($e), 'isError' => true];
                }
            }

            $messages[] = ['role' => 'assistant', 'content' => $response->content];
            $messages[] = ['role' => 'user', 'content' => $results];
        }

        return 'That needed more look-ups than I can do at once. Please ask a narrower question.';
    }

    /** Guide first (same for every restaurant, so it is cached), then this restaurant and person. */
    private function system(): array
    {
        static $guide;
        $guide ??= file_get_contents(module_path('Restaurant', 'resources/ai/assistant-guide.md'));

        $company = Tenancy::company();
        $name    = Setting::getValue($company->id, 'restaurant_name') ?: $company->name;

        return [
            ['type' => 'text', 'text' => self::INSTRUCTIONS . "\n\n" . $guide, 'cacheControl' => ['type' => 'ephemeral']],
            ['type' => 'text', 'text' => "Restaurant: {$name}\n"
                . "Signed-in person: {$this->user->name} (role: " . ($this->user->role?->name ?? 'none') . ")\n"
                . 'Now: ' . now()->format('l, d M Y h:i A') . ' (' . config('app.timezone') . ")\n"
                . 'Data look-ups available to this person: ' . (implode(', ', array_column((new AssistantTools($this->user))->definitions(), 'name')) ?: 'none') . '.'],
        ];
    }

    private const INSTRUCTIONS = <<<'TXT'
You are SajiloBhoj Support, the chat assistant inside a restaurant's SajiloBhoj admin panel.
You help restaurant owners and staff with two things:
1. How to use SajiloBhoj — answer from the system guide below. Point to the page with a Markdown link to its
   path, e.g. [Orders](/admin/orders).
2. Questions about their own restaurant — sales, orders, kitchen, tables, bookings, menu, stock, customers,
   staff, plan and balance. Always use the data look-up tools for these; never guess or invent numbers.
   If a look-up isn't available to this person, say their role doesn't include that section.

Answer like a helpful colleague: short, plain words, the answer first. Use Markdown only for **bold**,
short "- " lists and links. Money is "Nrs 1,234.00". Dates like "25 Nov 2026".
You can only read data; you can't change anything. When asked to make a change, explain the steps to do it
in the admin panel. If something isn't covered by the guide or the data, say you don't know and suggest
contacting the SajiloBhoj support team. Stay on topic: SajiloBhoj and running the restaurant.
TXT;

    /** Earlier turns from the browser: only plain text, alternating, starting with the person. */
    private function history(array $history): array
    {
        $turns = [];
        foreach (array_slice($history, -(int) config('restaurant.assistant.history_limit', 12)) as $turn) {
            $role = ($turn['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
            $text = trim(mb_substr((string) ($turn['content'] ?? ''), 0, 4000));
            if ($text === '' || ($turns === [] && $role !== 'user')) {
                continue;
            }
            $turns[] = ['role' => $role, 'content' => $text];
        }
        // The new question is added as a user turn, so the history must end with the assistant.
        while ($turns && end($turns)['role'] === 'user') {
            array_pop($turns);
        }

        return $turns;
    }

    private function text(array $content): string
    {
        $parts = [];
        foreach ($content as $block) {
            if (($block->type ?? null) === 'text') {
                $parts[] = $block->text;
            }
        }

        return trim(implode("\n\n", $parts));
    }
}
