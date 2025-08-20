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
                        $botRichContent = $this->convertProtobufValueToNativeOrStruct($richContentValue);
                    }
                }
            }

            $outputContexts = $queryResult->getOutputContexts()
                ? $this->convertProtobufContextsToNative($queryResult->getOutputContexts())
                : [];

            $botMessageData = [
                'session_id' => $this->sessionId,
                'message' => trim($botMessageText) ?: '💬 Bot đã gửi rich content.',
                'richContent' => $botRichContent,
                'created_at' => now()->toISOString(),
                'sender_type' => 'bot',
                'sender_id' => 'bot-system',
                'outputContexts' => $outputContexts,
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
                'outputContexts' => [],
            ]));
        } finally {
            $dialogflowService->close();
        }
    }

    private function convertProtobufStructToNative(Struct $struct)
    {
        $result = [];
        foreach ($struct->getFields() as $key => $value) {
            $result[$key] = $this->convertProtobufValueToNativeOrStruct($value);
        }
        return $result;
    }

    private function convertProtobufValueToNativeOrStruct($protobuf)
    {
        if ($protobuf instanceof Struct) {
            return $this->convertProtobufStructToNative($protobuf);
        } elseif ($protobuf instanceof Value) {
            if ($protobuf->getStructValue()) {
                return $this->convertProtobufStructToNative($protobuf->getStructValue());
            } elseif ($protobuf->getListValue()) {
                $list = [];
                foreach ($protobuf->getListValue()->getValues() as $item) {
                    $list[] = $this->convertProtobufValueToNativeOrStruct($item);
                }
                return $list;
            } elseif ($protobuf->getStringValue() !== null) {
                return (string) $protobuf->getStringValue();
            } elseif ($protobuf->getBoolValue() !== null) {
                return (bool) $protobuf->getBoolValue();
            } elseif ($protobuf->getNumberValue() !== null) {
                return (float) $protobuf->getNumberValue();
            } elseif ($protobuf->getNullValue() !== null) {
                return null;
            }
        }
        return null;
    }

    private function convertProtobufContextsToNative($protobufContexts)
    {
        $contexts = [];
        foreach ($protobufContexts as $ctx) {
            $contexts[] = [
                'name' => $ctx->getName(),
                'lifespanCount' => $ctx->getLifespanCount(),
                'parameters' => $this->convertProtobufStructToNative($ctx->getParameters()),
            ];
        }
        return $contexts;
    }
}
