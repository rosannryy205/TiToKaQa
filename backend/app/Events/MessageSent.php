<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
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
        $sessionId = $this->messageData['session_id'] ?? null;

        if ($sessionId) {
            $channelName = 'chat-session.' . $sessionId;
            Log::info('Broadcasting on channel: ' . $channelName);
            return [new Channel($channelName)];
        }

        Log::warning('No session_id found for broadcasting MessageSent event.');
        return [];
    }
}
