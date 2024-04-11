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
     * @param int $chatId description
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, int $chatId): \Illuminate\Http\JsonResponse
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

    /**
     * Send a JSON response after validating the request and creating a new message for the chat.
     *
     * @param Request $request The request object
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request): \Illuminate\Http\JsonResponse
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
}
