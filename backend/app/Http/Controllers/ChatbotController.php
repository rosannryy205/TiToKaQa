<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');

        if (!$message) {
            return response()->json(['error' => 'Không có nội dung!'], 400);
        }

        $client = new Client();

        try {
            $response = $client->post('https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model'    => 'openai/gpt-3.5-turbo',
                    'messages' => [['role' => 'user', 'content' => $message]],
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json(['reply' => $data['choices'][0]['message']['content'] ?? 'Lỗi: Không nhận được phản hồi!']);
        } catch (RequestException $e) {
            return response()->json(['error' => 'Lỗi API: ' . $e->getMessage()], 500);
        }
    }
}
