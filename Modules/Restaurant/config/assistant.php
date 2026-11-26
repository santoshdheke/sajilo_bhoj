<?php

/*
 * AI chat support in the admin panel (the floating chat button).
 * It answers from resources/ai/assistant-guide.md (how the system works) and from read-only
 * look-ups of the signed-in restaurant's own data (Modules\Restaurant\Support\Assistant\AssistantTools).
 * Without an API key the chat button stays, and says that AI support isn't set up yet.
 */
return [
    'api_key' => env('ANTHROPIC_API_KEY'),
    'model'   => env('ASSISTANT_MODEL', 'claude-opus-5-5'),
    // How hard the model thinks before answering: low | medium | high. Chat answers do well on low.
    'effort'  => env('ASSISTANT_EFFORT', 'low'),

    // Earlier messages sent along with each question (older ones are dropped).
    'history_limit' => 12,
    // Most data look-ups the assistant may run to answer one question.
    'max_tool_rounds' => 6,
    // Questions per person per minute.
    'per_minute' => 10,
];
