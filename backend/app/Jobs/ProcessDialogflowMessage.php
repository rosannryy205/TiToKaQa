<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\DialogflowService;
use Illuminate\Support\Facades\Log;
use App\Events\MessageSent;
use Google\Protobuf\Struct;
use Google\Protobuf\Value;
use Google\Protobuf\ListValue;

class ProcessDialogflowMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sessionId;
    protected $message;

    public function __construct($sessionId, $message)
    {
        $this->sessionId = $sessionId;
        $this->message = $message;
    }

    public function handle(DialogflowService $dialogflowService)
    {
        try {
            $queryResult = $dialogflowService->detectIntent($this->sessionId, $this->message);
            $botMessageText = '';
            $botRichContent = [];

            foreach ($queryResult->getFulfillmentMessages() as $message) {
                if ($text = $message->getText()?->getText()) {
                    $botMessageText .= implode("\n", iterator_to_array($text)) . "\n";
                }

                if ($payload = $message->getPayload()) {
                    $fields = $payload->getFields();
                    if ($fields->offsetExists('richContent')) {
                        $richContentValue = $fields->offsetGet('richContent');
                        $botRichContent = $this->convertProtobufValueToNative($richContentValue);
                    }
                }
            }

            $botMessageData = [
                'session_id' => $this->sessionId,
                'message' => trim($botMessageText) ?: '💬 Bot đã gửi rich content.',
                'richContent' => $botRichContent,
                'created_at' => now()->toISOString(),
                'sender_type' => 'bot',
                'sender_id' => 'bot-system',
            ];

            event(new MessageSent($botMessageData));
        } catch (\Exception $e) {
            Log::error('❌ Error in ProcessDialogflowMessage: ' . $e->getMessage());

            event(new MessageSent([
                'session_id' => $this->sessionId,
                'message' => '⚠️ Có lỗi xảy ra. Vui lòng thử lại!',
                'created_at' => now()->toISOString(),
                'sender_type' => 'bot',
                'sender_id' => 'bot-error',
            ]));
        } finally {
            $dialogflowService->close();
        }
    }

    private function convertProtobufValueToNative(Value $protobufValue)
    {
        if ($protobufValue->getStructValue()) {
            $struct = $protobufValue->getStructValue();
            $result = [];
            foreach ($struct->getFields() as $key => $value) {
                $result[$key] = $this->convertProtobufValueToNative($value);
            }
            return $result;
        } elseif ($protobufValue->getListValue()) {
            $list = [];
            foreach ($protobufValue->getListValue()->getValues() as $item) {
                $list[] = $this->convertProtobufValueToNative($item);
            }
            return $list;
        } elseif ($protobufValue->getStringValue() !== null) {
            return (string) $protobufValue->getStringValue();
        } elseif ($protobufValue->getBoolValue() !== null) {
            return (bool) $protobufValue->getBoolValue();
        } elseif ($protobufValue->getNumberValue() !== null) {
            return (float) $protobufValue->getNumberValue();
        } elseif ($protobufValue->getNullValue() !== null) {
            return null;
        }
        return null;
    }
}
