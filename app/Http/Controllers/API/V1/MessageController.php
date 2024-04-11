<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * A description of the entire PHP function.
     *
     * @param Request $request description
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'body' => 'required|string',
            'chat_id' => 'required|exists:chats,id',
        ]);

        $userId = auth()->id();
        $chatId = $request->chat_id;

        $chat = Chat::where('id', $chatId)
            ->where(function ($query) use ($userId) {
                $query
                    ->where('user1_id', $userId)
                    ->orWhere('user2_id', $userId);
            })
            ->firstOrFail();

        if (! $chat) {
            return response()->json('Chat not found', 404);
        }

        $message = Message::create([
            'body' => $request->body,
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);

        return response()->json($message);
    }

    /**
     * Retrieve messages by chat ID.
     *
     * @param Request $request The request object
     * @param $chatId The ID of the chat
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessagesByChat(Request $request, $chatId): \Illuminate\Http\JsonResponse
    {
        $userId = auth()->id();

        $messages = Message::whereHas('chat', function ($query) use ($chatId, $userId) {
            $query
                ->where('id', $chatId)
                ->where(function ($query) use ($userId) {
                    $query
                        ->where('user1_id', $userId)
                        ->orWhere('user2_id', $userId);
                });
        })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $optimizedMessages = $messages->map(function ($message) {
            return [
                'messageId' => $message->id,
                'timestamp' => $message->created_at,
                'text' => $message->body,
                'sender' => $message->user_id,
            ];
        });

        return response()->json($optimizedMessages);
    }
}
