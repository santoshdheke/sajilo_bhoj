<?php

namespace Modules\Restaurant\Http\Controllers\Admin;

use Anthropic\Core\Exceptions\APIConnectionException;
use Anthropic\Core\Exceptions\AuthenticationException;
use Anthropic\Core\Exceptions\OverloadedException;
use Anthropic\Core\Exceptions\RateLimitException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Restaurant\Support\Assistant\Assistant;

/** The floating chat button in the admin panel: questions go to the AI assistant. */
class AssistantController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message'           => 'required|string|max:1000',
            'history'           => 'nullable|array|max:40',
            'history.*.role'    => 'required|in:user,assistant',
            'history.*.content' => 'required|string|max:4000',
        ]);

        if (! Assistant::configured()) {
            return response()->json(['reply' => 'AI support isn’t set up yet. Please ask the SajiloBhoj team to add the AI key.'], 503);
        }

        try {
            $reply = (new Assistant($request->user()))->reply($data['message'], $data['history'] ?? []);
        } catch (RateLimitException|OverloadedException) {
            return response()->json(['reply' => 'I’m getting a lot of questions right now. Please try again in a minute.'], 503);
        } catch (APIConnectionException) {
            return response()->json(['reply' => 'I couldn’t reach the AI service. Please check the internet connection and try again.'], 503);
        } catch (AuthenticationException $e) {
            report($e);

            return response()->json(['reply' => 'AI support isn’t set up correctly (the AI key was rejected). Please tell the SajiloBhoj team.'], 503);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['reply' => 'Sorry, something went wrong. Please try again.'], 500);
        }

        return response()->json(['reply' => $reply]);
    }
}
