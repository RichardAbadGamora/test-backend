<?php

namespace App\Services;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Http;

class ChatMessageService
{
    public function createChatMessage($userId, $pageId, $session, $content)
    {
        return ChatMessage::create([
            'user_id' => $userId,
            'page_id' => $pageId,
            'session' => $session,
            'type' => 'prompt',
            'content' => $content,
        ]);
    }

    public function generateChatResponse($content)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('app.openai_api_key'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ],
        ]);

        return $response->json('choices.0.message.content');
    }

    public function createResponseChatMessage($userId, $pageId, $session, $content)
    {
        return ChatMessage::create([
            'user_id' => $userId,
            'page_id' => $pageId,
            'session' => $session,
            'type' => 'response',
            'content' => $content,
        ]);
    }
}
