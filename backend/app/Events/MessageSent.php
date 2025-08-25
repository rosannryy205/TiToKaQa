<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
<<<<<<< HEAD
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Import Log nếu dùng để debug

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageData; // Tên public để Pusher có thể truy cập

    /**
     * Create a new event instance.
     */
    public function __construct(array $messageData) // <-- Phải là 'array $messageData'
    {
        $this->messageData = $messageData;
        Log::info('MessageSent event constructed with data:', $messageData); // Thêm log để xác nhận dữ liệu nhận được
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Lấy session_id từ mảng dữ liệu
=======
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageData;

    public function __construct(array $messageData)
    {
        $this->messageData = $messageData;
        Log::info('MessageSent event constructed with data:', $messageData);
    }

    public function broadcastOn(): array
    {
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
        $sessionId = $this->messageData['session_id'] ?? null;

        if ($sessionId) {
            $channelName = 'chat-session.' . $sessionId;
<<<<<<< HEAD
            Log::info('Broadcasting on channel: ' . $channelName); // Log tên kênh
            return [new Channel($channelName)];
        }

        // Fallback hoặc xử lý lỗi nếu không có session_id
        Log::warning('No session_id found for broadcasting MessageSent event.');
        return []; // Không broadcast nếu không có session_id hợp lệ
=======
            Log::info('Broadcasting on channel: ' . $channelName);
            return [new Channel($channelName)];
        }

        Log::warning('No session_id found for broadcasting MessageSent event.');
        return [];
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
    }
}
