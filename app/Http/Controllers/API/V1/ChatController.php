<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\ChatRequest;

class ChatController extends Controller
{
    /**
     * Retrieves chats associated with the authenticated user, optimizes the data, and returns it in a JSON response.
     *
     * @param Request $request The HTTP request object
     * @return \Illuminate\Http\JsonResponse The JSON response containing optimized chat data
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user_id = auth()->id();

        $chats = Chat::select('id', 'name', 'user1_id', 'user2_id', 'updated_at')
            ->where('user1_id', $user_id)
            ->orWhere('user2_id', $user_id)
            ->orderByDesc('updated_at')
            ->paginate(20);

        $optimizedChats = $chats->map(function ($chat) use ($user_id) {
            $partnerId = $chat->user1_id === $user_id ? $chat->user2_id : $chat->user1_id;

            $partner = User::findOrFail($partnerId);

            $chatTitle = $partner->name . ' ' . $partner->last_name;

            return [
                'chatId' => $chat->id,
                'timestamp' => $chat->updated_at,
                'participants' => [$user_id, $partnerId],
                'chatTitle' => $chatTitle,
            ];
        });

        return response()->json($optimizedChats);
    }

    /**
     * Create and returns a JSON response.
     *
     * @param ChatRequest $request description
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ChatRequest $request): \Illuminate\Http\JsonResponse
    {
        $user1_id = auth()->id();
        $user2_id = $request->user_id;
        $name = $request->name;

        $chat = Chat::where(function ($query) use ($user1_id, $user2_id, $name) {
            $query
                ->where('user1_id', $user1_id)
                ->where('user2_id', $user2_id)
                ->where('name', $name);
        })->orWhere(function ($query) use ($user1_id, $user2_id, $name) {
            $query
                ->where('user1_id', $user2_id)
                ->where('user2_id', $user1_id)
                ->where('name', $name);
        })->first();

        if (! $chat) {
            $chat = Chat::create([
                'name' => $name,
                'user1_id' => $user1_id,
                'user2_id' => $user2_id,
            ]);
        }

        return response()->json($chat);
    }
}
