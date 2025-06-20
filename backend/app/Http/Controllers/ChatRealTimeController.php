<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatRealTimeController extends Controller
{
    public function sendMessage(Request $request)
    {
        $messageText = $request->input('message');
        $senderName = $request->input('sender_name') ?? 'Khách_' . time();
        $conversationId = $request->input('conversation_id');
        $isGuest = $request->input('is_guest');

        $senderUserId = $isGuest ? null : $request->input('sender_id');
        $senderGuestId = $isGuest ? $request->input('sender_id') : null;

        $receiverUserId = $request->input('receiver_user_id');
        $receiverGuestId = $request->input('receiver_guest_id');

        $avatar = null;

        if (!$conversationId) {
            if ($isGuest) {
                $response = Http::get('https://api.waifu.pics/sfw/waifu');
                $avatar = $response->json()['url'];
            } else {
                $avatar = User::find($senderUserId)->avatar;
            }

            $conversation = Conversation::create([
                'sender_user_id' => $senderUserId,
                'sender_guest_id' => $senderGuestId,
                'receiver_user_id' => $receiverUserId,
                'receiver_guest_id' => $receiverGuestId,
                'sender_name' => $senderName,
                'sender_avatar' => $avatar,
            ]);
        } else {
            $conversation = Conversation::find($conversationId);
        }

        $message = Message::create([
            'sender_user_id' => $senderUserId,
            'sender_guest_id' => $senderGuestId,
            'receiver_user_id' => $receiverUserId,
            'receiver_guest_id' => $receiverGuestId,
            'conversation_id' => $conversation->id,
            'message' => $messageText,
        ]);

        $conversation->update(['is_read' => 0, 'last_message_at' => now()]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    public function getMessages(Request $request)
    {
        $conversationId = $request->input('conversation_id');
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function getConversationToAdmin(Request $request)
    {
        $status = $request->input('status');
        $receiverId = $request->input('receiver_id');
        $query = Conversation::with('lastMessage')
            ->where('status', $status);

        if (in_array($status, ['Đang xử lý', 'Đã xử lý']) && $receiverId) {
            $query->where('receiver_user_id', $receiverId);
        }


        $conversations = $query->orderByDesc('updated_at')->get();

        return response()->json($conversations);
    }


    public function assignAdmin(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        if (is_null($conversation->receiver_user_id)) {
            $conversation->update([
                'receiver_user_id' => $request->receiver_id,
                'status' => 'Đang xử lý',
                'is_read' => 1,
            ]);
        } else {
            $conversation->update([
                'is_read' => 1
            ]);
        }

        Message::where('conversation_id', $conversation->id)
            ->update([
                'receiver_user_id' => $request->receiver_id,
                'receiver_guest_id' => null,
            ]);

        return response()->json([
            'message' => 'Đã xử lý gán hoặc cập nhật trạng thái đọc.',
            'conversation' => $conversation
        ]);
    }

    public function markRead($id)
    {
        $conversation = Conversation::find($id);

        if (!$conversation) {
            return response()->json(['message' => 'Không tìm thấy cuộc trò chuyện'], 404);
        }

        $conversation->update(['is_read' => 1]);

        return response()->json([
            'message' => 'Đã đánh dấu đã đọc',
            'conversation' => $conversation
        ]);
    }
}
