<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Reservation_table;
use App\Models\Table;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function InfoReservationByChatBot(Request $request)
    {

        // $intent = $request->input('queryResult.intent.displayName');

        // if ($intent === 'SuaThongTin') {
        //     return response()->json([
        //         'fulfillmentText' => 'Bạn muốn sửa lại thông tin đặt bàn. Mình sẽ hỏi lại từng thông tin nha!',
        //         'outputContexts' => [
        //             [
        //                 'name' => $request->input('session') . '/contexts/reservation-info',
        //                 'lifespanCount' => 5,
        //                 'parameters' => [
        //                     'etenkhach' => null,
        //                     'guest_phone' => null,
        //                     'guest_email' => null,
        //                     'reservation_date' => null,
        //                     'reservation_time' => null,
        //                     'guest_count' => null,
        //                 ]
        //             ]
        //         ]
        //     ]);
        // }

        $params = $request->input('queryResult.parameters');
        $guestName = is_array($params['etenkhach'] ?? null) ? $params['etenkhach'][0] : ($params['etenkhach'] ?? null);
        $guestPhone = is_array($params['guest_phone'] ?? null) ? $params['guest_phone'][0] : ($params['guest_phone'] ?? null);
        $guestEmail = is_array($params['guest_email'] ?? null) ? $params['guest_email'][0] : ($params['guest_email'] ?? null);
        $numberOfGuests = is_array($params['guest_count'] ?? null) ? $params['guest_count'][0] : ($params['guest_count'] ?? null);

        $date = $params['reservation_date'] ?? null;
        $time = $params['reservation_time'] ?? null;
        if (is_array($date)) $date = $date[0];
        if (is_array($time)) $time = $time[0];

        $fromDate = new DateTime($time);
        $formattedTime = $fromDate->format('H:i');
        $formattedDate = $fromDate->format('d/m/Y');

        return response()->json([
            'fulfillmentMessages' => [
                [
                    'text' => [
                        'text' => [
                            "📋 Mình xin xác nhận lại thông tin đặt bàn như sau:\n" .
                                "• Tên khách: $guestName\n" .
                                "• Số điện thoại: $guestPhone\n" .
                                "• Email: $guestEmail\n" .
                                "• Thời gian: $formattedTime - $formattedDate\n" .
                                "• Số người: $numberOfGuests\n" .
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

        $conflictingTableIds = DB::table('reservation_tables')
            ->join('orders', 'reservation_tables.order_id', '=', 'orders.id')
            ->whereNotIn('orders.order_status', ['Đã hủy', 'Hoàn Thành'])
            ->where('reserved_from', '<', $to)
            ->where('reserved_to', '>', $from)
            ->pluck('reservation_tables.table_id')
            ->toArray();

        $availableTables = Table::whereNotIn('id', $conflictingTableIds)
            ->where('capacity', '>=', $numberOfGuests)
            ->orderBy('capacity', 'asc')
            ->orderBy('table_number', 'asc')
            ->get();

        if ($availableTables->isEmpty()) {
            return response()->json([
                'fulfillmentText' => 'Xin lỗi, hiện tại không còn bàn trống phù hợp. Bạn thử lại thời gian khác nhé!'
            ]);
        }

        $table = $availableTables->first();
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
        ]);

        Reservation_table::create([
            'order_id' => $order->id,
            'table_id' => $table->id,
            'reserved_from' => $from,
            'reserved_to' => $reserved_to,
        ]);

        $table->update([
            'status' => 'Đã đặt trước',
        ]);

        return response()->json([
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
    }


    public function combine(Request $request)
    {
        $intent = $request->input('queryResult.intent.displayName');

        if ($intent === 'DatBan' || $intent === 'SuaThongTin') {
            return $this->InfoReservationByChatBot($request);
        } elseif ($intent === 'XacNhan') {
            return $this->ReservationByChatBot($request);
        }
    }
}
