<?php

namespace App\Http\Controllers;

use App\Mail\ReservationMail;
use App\Models\Order;
use App\Models\Reservation_table;
use App\Models\Table;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendReservationMail;
use Illuminate\Support\Str;

Carbon::setLocale('vi');
date_default_timezone_set('Asia/Ho_Chi_Minh');
class ChatbotController extends Controller
{
    public function InfoReservationByChatBot(Request $request)
    {
        // $outputContexts = $request->input('queryResult.outputContexts');
        // $reservationContext = collect($outputContexts)->first(function ($ctx) {
        //     return str_ends_with($ctx['name'], '/contexts/reservation-info');
        // });

        // if (!$reservationContext) {
        //     Log::warning('reservation-info context not found or invalid.');
        //     return response()->json([
        //         'fulfillmentText' => 'Xin lỗi, có vẻ như đã xảy ra lỗi trong việc duy trì thông tin đặt bàn. Vui lòng thử lại.'
        //     ]);
        // }

        $params = $request->input('queryResult.parameters');


        $guestName = is_array($params['etenkhach'] ?? null) && !empty($params['etenkhach']) ? $params['etenkhach'][0] : ($params['etenkhach'] ?? null);
        $guestPhone = is_array($params['guest_phone'] ?? null) && !empty($params['guest_phone']) ? $params['guest_phone'][0] : ($params['guest_phone'] ?? null);
        $guestEmail = is_array($params['guest_email'] ?? null) && !empty($params['guest_email']) ? $params['guest_email'][0] : ($params['guest_email'] ?? null);
        $numberOfGuests = is_array($params['guest_count'] ?? null) && !empty($params['guest_count']) ? $params['guest_count'][0] : ($params['guest_count'] ?? null);

        $time = null;
        if (isset($params['reservation_time'])) {
            if (is_array($params['reservation_time']) && !empty($params['reservation_time'])) {
                $time = $params['reservation_time'][0];
            } else if (is_string($params['reservation_time'])) {
                $time = $params['reservation_time'];
            }
        }

        $date = null;
        if (isset($params['reservation_date'])) {
            if (is_array($params['reservation_date']) && !empty($params['reservation_date'])) {
                $date = $params['reservation_date'][0];
            } else if (is_string($params['reservation_date'])) {
                $date = $params['reservation_date'];
            }
        }

        $formattedTime = 'chưa có';
        $formattedDate = 'chưa có';
        if ($time) {
            $fromDate = new DateTime($time);
            $formattedTime = $fromDate->format('H:i');
        }
        if ($date) {
            $fromDateDate = new DateTime($date);
            $formattedDate = $fromDateDate->format('d/m/Y');
        }


        return response()->json([

            'fulfillmentMessages' => [
                [
                    'text' => [
                        'text' => [
                            "📋 Mình xin xác nhận lại thông tin đặt bàn như sau:\n" .
                                "• Tên khách: " . ($guestName ?: 'chưa có') . "\n" .
                                "• Số điện thoại: " . ($guestPhone ?: 'chưa có') . "\n" .
                                "• Email: " . ($guestEmail ?: 'chưa có') . "\n" .
                                "• Thời gian: $formattedTime - $formattedDate\n" .
                                "• Số người: " . ($numberOfGuests ?: 'chưa có') . "\n" .
                                "Bạn vui lòng kiểm tra lại thông tin trên nhé!"
                        ]
                    ]
                ],
                [
                    'payload' => [
                        'richContent' => [
                            [
                                [
                                    'type' => 'chips',
                                    'options' => [
                                        ['text' => '✅ Xác nhận'],
                                        ['text' => '❌ Sửa lại'],
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'outputContexts' => [
                [
                    'name' => $request->input('session') . '/contexts/reservation-info',
                    'lifespanCount' => 5,
                    'parameters' => [
                        'etenkhach' => $guestName,
                        'guest_phone' => $guestPhone,
                        'guest_email' => $guestEmail,
                        'reservation_date' => $date,
                        'reservation_time' => $time,
                        'guest_count' => $numberOfGuests,
                    ]
                ]
            ]
        ]);
    }

    private function generateReservationCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Order::where('reservation_code', $code)->exists());

        return $code;
    }

    public function ReservationByChatBot(Request $request)
    {
        $outputContexts = $request->input('queryResult.outputContexts');
        $reservationContext = collect($outputContexts)->first(function ($ctx) {
            return str_ends_with($ctx['name'], '/contexts/reservation-info');
        });

        $params = $reservationContext['parameters'] ?? [];

        $guestName = $params['etenkhach'] ?? '';
        $guestPhone = $params['guest_phone'] ?? '';
        $guestEmail = $params['guest_email'] ?? '';
        $numberOfGuests = $params['guest_count'] ?? null;
        $date = $params['reservation_date'] ?? '';
        $time = $params['reservation_time'] ?? '';

        $from = $time;
        $to = (new DateTime($from))->modify('+2 hours')->format('Y-m-d H:i:s');
        $reservationDateTime = new DateTime($from);

        $now = Carbon::now();
        if ($reservationDateTime < $now) {
            return response()->json([
                'fulfillmentMessages' => [
                    [
                        'text' => [
                            'text' => [
                                "⛔ Bạn không thể đặt bàn ở thời điểm đã qua. Vui lòng chọn thời gian trong tương lai."
                            ]
                        ]
                    ]
                ]
            ]);
        }

        $hour = (int)$reservationDateTime->format('H');
        $minute = (int)$reservationDateTime->format('i');

        $isValidTime = false;
        if ($hour >= 8 && $hour < 21) {
            $isValidTime = true;
        } elseif ($hour === 21 && $minute <= 30) {
            $isValidTime = true;
        }

        if (!$isValidTime) {
            return response()->json([
                'fulfillmentMessages' => [
                    [
                        'text' => [
                            'text' => [
                                "⏰ Thời gian phục vụ chỉ từ 08:00 đến 21:30. Vui lòng chọn lại thời gian đặt bàn phù hợp."
                            ]
                        ]
                    ]
                ]
            ]);
        }

        $conflictingTableIds = DB::table('reservation_tables')
            ->join('orders', 'reservation_tables.order_id', '=', 'orders.id')
            ->whereNotIn('orders.order_status', ['Đã hủy', 'Hoàn Thành'])
            ->where('reserved_from', '<', $to)
            ->where('reserved_to', '>', $from)
            ->pluck('reservation_tables.table_id')
            ->toArray();

        $availableTables = Table::whereNotIn('id', $conflictingTableIds)
            ->orderBy('table_number', 'asc')
            ->get();

        $singleTable = $availableTables->firstWhere('capacity', '>=', $numberOfGuests);
        $selectedTables = collect();
        if ($singleTable) {
            $selectedTables->push($singleTable);
        } else {
            $tempGroup = collect();
            $totalCap = 0;
            $prevTableNum = null;

            foreach ($availableTables as $table) {
                if ($prevTableNum === null || $table->table_number == $prevTableNum + 1) {
                    $tempGroup->push($table);
                    $totalCap += $table->capacity;
                    $prevTableNum = $table->table_number;

                    if ($totalCap >= $numberOfGuests) {
                        $selectedTables = $tempGroup;
                        break;
                    }
                } else {
                    $tempGroup = collect([$table]);
                    $totalCap = $table->capacity;
                    $prevTableNum = $table->table_number;
                }
            }
        }

        if ($selectedTables->isEmpty()) {
            return response()->json([
                'fulfillmentMessages' => [
                    [
                        'text' => [
                            'text' => [
                                "Xin lỗi, hiện tại không còn bàn trống phù hợp. Bạn thử lại thời gian khác hoặc liên hệ để được hỗ trợ nhé!."
                            ]
                        ]
                    ]
                ]
            ]);
        }
        $orderTime = Carbon::now();
        $reserved_to = date('Y-m-d H:i:s', strtotime($from . ' +2 hours'));

        $order = Order::create([
            'guest_name' => $guestName,
            'guest_phone' => $guestPhone,
            'guest_count' => $numberOfGuests,
            'order_time' => $orderTime,
            'guest_email' => $guestEmail,
            'expiration_time' => $orderTime->copy()->addMinutes(15),
            'reservation_time' => $from,
            'reservation_code' => $this->generateReservationCode(),

        ]);

        foreach ($selectedTables as $table) {
            Reservation_table::create([
                'order_id' => $order->id,
                'table_id' => $table->id,
                'reserved_from' => $from,
                'reserved_to' => $reserved_to,
            ]);

            $table->update(['status' => 'Đã đặt trước']);
        }

        $response = response()->json([
            'fulfillmentMessages' => [
                [
                    'text' => [
                        'text' => [
                            "✅ Đặt bàn thành công! Hẹn gặp bạn lúc " . (new DateTime($from))->format('H:i d/m/Y') . "."
                        ]
                    ]
                ]
            ]
        ]);
        $response->send();
        Log::info('Đã gửi phản hồi');

        // fastcgi_finish_request();

        $tableInfos = $order->tables->map(function ($table) {
            return [
                'table_number'  => $table->table_number ?? 'Không rõ',
                'reserved_from' => $table->pivot->reserved_from,
                'reserved_to'   => $table->pivot->reserved_to,
            ];
        })->toArray();

        $qrImage = QrCode::format('png')->size(250)->generate('http://localhost:5173/history-order-detail/' . $order->id);

        $filename = 'qr_' . $order->id . '.png';
        $tempPath = storage_path('app/public/' . $filename);
        file_put_contents($tempPath, $qrImage);

        $uploadedFileUrl = Cloudinary::upload($tempPath, [
            'folder' => 'qr_codes'
        ])->getSecurePath();

        unlink($tempPath);

        $mailData = [
            'order_id' => $order->id,
            'reservation_code' => $order->reservation_code,
            'guest_name' => $guestName,
            'guest_email' => $guestEmail,
            'guest_phone' => $guestPhone,
            'guest_count' => $numberOfGuests,
            'total_price' => $request->total_price ?? null,
            'note' => $request->note ?? null,
            'order_details' => null,
            'tables' => $tableInfos,
            'subtotal' => null,
            'order_status' =>  'Đã xác nhận',
            'qr_url' => $uploadedFileUrl
        ];


        // Mail::to($mailData['guest_email'])->send(new ReservationMail($mailData));
        dispatch(new SendReservationMail($mailData));
        Log::info('Đã đẩy job gửi mail vào hàng đợi');
    }


    public function CancelReservationByChatBot(Request $request)
    {
        try {
            $params = $request->input('queryResult.parameters');
            $code = is_array($params['reservation_code'] ?? null) ? $params['reservation_code'][0] : ($params['reservation_code'] ?? null);

            $order = Order::where('reservation_code', $code)->first();

            if (!$order) {
                return response()->json([
                    'fulfillmentText' => "Không tìm thấy đơn đặt bàn với mã đặt $code."
                ]);
            }

            $order->order_status = 'Đã hủy';
            $order->save();

            return response()->json([
                'fulfillmentText' => "✅ Đơn đặt bàn của bạn đã được hủy thành công!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'fulfillmentText' => 'Có lỗi xảy ra. Vui lòng thử lại sau.',
            ], 200);
        }
    }

    public function combine(Request $request)
    {
        $intent = $request->input('queryResult.intent.displayName');
        Log::info('Webhook intent: ' . $intent);

        // || $intent === 'TenKhach' || $intent === 'SoDienThoai' || $intent === 'NgayDat' || $intent === 'GioDat' || $intent === 'SoLuongNguoi' || $intent === 'Email'
        if ($intent === 'DatBan' || $intent === 'SuaThongTin') {
            return $this->InfoReservationByChatBot($request);
        } elseif ($intent === 'XacNhan') {
            return $this->ReservationByChatBot($request);
        } elseif ($intent === 'HuyDon') {
            return $this->CancelReservationByChatBot($request);
        } else {
            return response()->json([
                'fulfillmentText' => 'Xin lỗi, tôi không hiểu yêu cầu của bạn.'
            ]);
        }
    }
}
