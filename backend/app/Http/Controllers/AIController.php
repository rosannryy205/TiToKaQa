<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function generatePost(Request $request)
    {
        $title = $request->input('title');

        //Gọi Gemini API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => env('GEMINI_API_KEY'),
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent', [
            'contents' => [[
                'parts' => [[
                    'text' => "Bạn là một chuyên gia sáng tạo nội dung cho nhà hàng Titokaqa. Viết một bài giới thiệu vừa đủ dài, hấp dẫn và chuẩn SEO về chủ đề {$title}. Bài viết có thể thuộc nhiều thể loại như:
- Ẩm thực: miêu tả hương vị, cách chế biến món ăn, gợi mở cảm giác thèm ăn.
- Khuyến mãi: nêu rõ ưu đãi, thời gian áp dụng, khuyến khích đặt bàn hoặc đặt món online.
- Tin tức: cập nhật sự kiện, hoạt động, hoặc thông tin mới của nhà hàng.
- Liên hệ: 0919943410, titokaqarestaurant@gmail.com.
- Địa chỉ: QTSC 9 Building, Đ. Tô Ký, Tân Chánh Hiệp, Quận 12, Hồ Chí Minh.
- Link web: titokaqarestaurant.com.vn
Nội dung phải bắt mắt, cuốn hút, dễ đọc, sử dụng từ khóa hợp lý liên quan đến nhà hàng Titokaqa, và kèm icon phù hợp."

                ]]
            ]]
        ]);

        $data = $response->json();

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return response()->json([
                'content' => $data['candidates'][0]['content']['parts'][0]['text']
            ]);
        } else {
            return response()->json([
                'error' => 'No content returned form Gemini',
                'raw' => $data
            ], 500);
        }
    }

    public function checkSeo(Request $request)
    {
        $title = $request->input('title');
        $content = $request->input('content');

        if (!$title || !$content) {
            return response()->json(['error' => 'Thiếu tiêu đề hoặc nội dung'], 400);
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . env('GEMINI_API_KEY'),
            [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "Phân tích bài viết sau về mức độ chuẩn SEO.
                            Tiêu đề: \"$title\"
                            Nội dung: \"$content\"
                            Hãy chỉ trả về JSON **thuần**, không giải thích thêm, với format:
                            {
                              \"score\": số từ 0-100,
                              \"strengths\": [danh sách ưu điểm],
                              \"weaknesses\": [danh sách nhược điểm],
                              \"recommendations\": [danh sách gợi ý cải thiện]
                            }"
                            ]
                        ]
                    ]
                ]
            ]
        );

        $data = $response->json();

        $aiText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($aiText) {
            // Xóa ký tự thừa trước/sau JSON
            $cleanJson = trim($aiText);
            $cleanJson = preg_replace('/```json|```/', '', $cleanJson);

            $seoData = json_decode($cleanJson, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return response()->json($seoData);
            } else {
                return response()->json([
                    'error' => 'AI trả về nhưng không phải JSON hợp lệ',
                    'raw' => $aiText
                ], 500);
            }
        }

        return response()->json([
            'error' => 'No content returned from AI',
            'raw' => $data
        ], 500);
    }
}
