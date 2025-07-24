<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Validation\ValidationException;
use App\Jobs\ProcessDialogflowMessage;
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
use App\Models\Combo;
use App\Models\Food;
use App\Models\Food_topping;
use App\Models\Order_detail;
use App\Models\Order_topping;
use App\Services\DialogflowService;
use Exception;
use Illuminate\Support\Str;

use function ElementorDeps\DI\string;

Carbon::setLocale('vi');
date_default_timezone_set('Asia/Ho_Chi_Minh');
class ChatbotController extends Controller
{

    protected $dialogflowService;

    public function __construct(DialogflowService $dialogflowService)
    {
        $this->dialogflowService = $dialogflowService;
    }

    public function handleMessage(Request $request)
    {
        $sessionId = $request->input('session_id');
        $message = $request->input('message');
        if (!$sessionId || !$message) {
            return response()->json(['error' => 'Thiếu session_id hoặc message.'], 400);
        }

        try {
            ProcessDialogflowMessage::dispatch($sessionId, $message);

            return response()->json(['status' => 'Tin nhắn đang được xử lý.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể xử lý yêu cầu.'], 500);
        }
    }

    private function getReservationParams(Request $request)
    {
        $outputContexts = $request->input('queryResult.outputContexts');

        $reservationContext = collect($outputContexts)->first(function ($ctx) {
            return str_ends_with($ctx['name'], '/contexts/reservation-info');
        });

        if (!$reservationContext) return null;

        return $reservationContext['parameters'] ?? [];
    }

    public function checkGuestName(Request $request)
    {
        $params = $this->getReservationParams($request);

        if (!$params) {
            return response()->json([
                'fulfillmentText' => 'Không lấy được thông tin đặt bàn. Vui lòng thử lại.'
            ]);
        }

        $guestName = is_array($params['guest_name'] ?? null) && !empty($params['guest_name'])
            ? $params['guest_name'][0]
            : ($params['guest_name'] ?? null);

        if (!preg_match('/^[a-zA-ZÀ-ỹ\s]{2,50}$/u', $guestName) || strlen(trim($guestName)) < 2) {
            return response()->json([
                'fulfillmentText' => '❌ Tên không hợp lệ. Vui lòng nhập lại tên (chỉ gồm chữ cái).'
            ]);
        }


        return response()->json([
            'fulfillmentText' => ''
        ]);
    }

    public function checkGuestPhone(Request $request)
    {
        $params = $this->getReservationParams($request);

        if (!$params) {
            return response()->json([
                'fulfillmentText' => 'Không lấy được thông tin đặt bàn. Vui lòng thử lại.'
            ]);
        }

        $guestPhone = is_array($params['guest_phone'] ?? null) && !empty($params['guest_phone']) ? $params['guest_phone'][0] : ($params['guest_phone'] ?? null);
        if (!preg_match('/^(0|\+84)(3[2-9]|5[689]|7[06789]|8[1-6]|9[0-9])[0-9]{7}$/', $guestPhone)) {
            return response()->json([
                'fulfillmentText' => 'Số điện thoại không hợp lệ. Bạn vui lòng nhập số gồm 10 chữ số, bắt đầu bằng 0 hoặc +84 nhé!'
            ]);
        }

        return response()->json([
            'fulfillmentText' => ''
        ]);
    }

    public function checkGuestEmail(Request $request)
    {
        $params = $this->getReservationParams($request);

        if (!$params) {
            return response()->json([
                'fulfillmentText' => 'Không lấy được thông tin đặt bàn. Vui lòng thử lại.'
            ]);
        }

        $guestEmail = is_array($params['guest_email'] ?? null) && !empty($params['guest_email']) ? $params['guest_email'][0] : ($params['guest_email'] ?? null);
        if (!filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'fulfillmentText' => 'Email không hợp lệ. Bạn vui lòng nhập đúng định dạng email (vd: abc@gmail.com) nhé!'
            ]);
        }

        return response()->json([
            'fulfillmentText' => ''
        ]);
    }

    public function checkGuestCount(Request $request)
    {
        $params = $this->getReservationParams($request);

        if (!$params) {
            return response()->json([
                'fulfillmentText' => 'Không lấy được thông tin đặt bàn. Vui lòng thử lại.'
            ]);
        }

        $numberOfGuests = is_array($params['guest_count'] ?? null) && !empty($params['guest_count']) ? $params['guest_count'][0] : ($params['guest_count'] ?? null);
        if (!is_numeric($numberOfGuests) || $numberOfGuests < 1 || $numberOfGuests > 20) {
            return response()->json([
                'fulfillmentText' => 'Số lượng người không hợp lệ. Bạn vui lòng nhập số từ 1 đến 20 nhé!'
            ]);
        }

        return response()->json([
            'fulfillmentText' => ''
        ]);
    }

    public function checkReservationTime(Request $request)
    {
        $params = $this->getReservationParams($request);

        if (!$params) {
            return response()->json([
                'fulfillmentText' => 'Không lấy được thông tin đặt bàn. Vui lòng thử lại.'
            ]);
        }

        $time = null;
        if (isset($params['reservation_time'])) {
            if (is_array($params['reservation_time']) && !empty($params['reservation_time'])) {
                $time = $params['reservation_time'][0];
            } else if (is_string($params['reservation_time'])) {
                $time = $params['reservation_time'];
            }
        }

        if ($time) {
            $timeObj = new DateTime($time);
            $hour = (int)$timeObj->format('H');
            $minute = (int)$timeObj->format('i');

            $isValidTime = ($hour >= 8 && ($hour < 21 || ($hour === 21 && $minute <= 30)));

            if (!$isValidTime) {
                return response()->json([
                    'fulfillmentText' => '⏰ Thời gian phục vụ chỉ từ 08:00 đến 21:30. Vui lòng chọn lại thời gian đặt bàn phù hợp.'
                ]);
            }
        }
        return $this->InfoReservationByChatBot($request);
    }

    public function InfoReservationByChatBot(Request $request)
    {
        $start = microtime(true);

        $outputContexts = $request->input('queryResult.outputContexts');

        $reservationContext = collect($outputContexts)->first(function ($ctx) {
            return str_ends_with($ctx['name'], '/contexts/reservation-info');
        });

        if (!$reservationContext) {
            return response()->json([
                'fulfillmentText' => 'Có lỗi trong việc duy trì thông tin đặt bàn. Vui lòng thử lại.'
            ]);
        }

        $params = $reservationContext['parameters'] ?? [];
        Log::info('Params: ', $params);


        $guestName = is_array($params['guest_name'] ?? null) && !empty($params['guest_name']) ? $params['guest_name'][0] : ($params['guest_name'] ?? null);
        $guestPhone = is_array($params['guest_phone'] ?? null) && !empty($params['guest_phone']) ? $params['guest_phone'][0] : ($params['guest_phone'] ?? null);
        $guestEmail = is_array($params['guest_email'] ?? null) && !empty($params['guest_email']) ? $params['guest_email'][0] : ($params['guest_email'] ?? null);
        $numberOfGuests = is_array($params['guest_count'] ?? null) && !empty($params['guest_count']) ? $params['guest_count'][0] : ($params['guest_count'] ?? null);
        $time = null;
        $date = null;

        if (isset($params['reservation_date'])) {
            if (is_array($params['reservation_date'])) {
                if (isset($params['reservation_date']['date_time'])) {
                    $time = $params['reservation_date']['date_time'];
                    $date = $params['reservation_date']['date_time'];
                }
            } else if (is_string($params['reservation_date'])) {
                $time = $params['reservation_date'];
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

        $duration = microtime(true) - $start;
        Log::info('InfoReservationByChatBot duration: ' . $duration);

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
                                        [
                                            'text' => '✅ Xác nhận',
                                            'postback' => '✅ Xác nhận' // Thêm postback cho nút "Xác nhận"
                                        ],
                                        [
                                            'text' => '❌ Sửa lại',
                                            'postback' => '❌ Sửa lại' // Thêm postback cho nút "Sửa lại"
                                        ],
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
                        'guest_name' => $guestName,
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

    public function checkReservationDate(Request $request)
    {
        $params = $this->getReservationParams($request);

        if (!$params) {
            return response()->json([
                'fulfillmentText' => 'Không lấy được thông tin đặt bàn. Vui lòng thử lại.'
            ]);
        }

        $date = null;
        if (isset($params['reservation_date'])) {
            if (is_array($params['reservation_date']) && !empty($params['reservation_date'])) {
                $date = $params['reservation_date'][0];
            } else if (is_string($params['reservation_date'])) {
                $date = $params['reservation_date'];
            }
        }

        if ($date) {
            $dateObj = new DateTime($date);
            $today = new DateTime('today');

            if ($dateObj < $today) {
                return response()->json([
                    'fulfillmentText' => '⛔ Bạn không thể đặt bàn ở thời điểm đã qua. Vui lòng chọn thời gian trong tương lai.'
                ]);
            }
        }

        return $this->InfoReservationByChatBot($request);
    }


    // public function checkInfoReservationByChatBot(Request $request, $params)
    // {
    //     $outputContexts = $request->input('queryResult.outputContexts');

    //     $reservationContext = collect($outputContexts)->first(function ($ctx) {
    //         return str_ends_with($ctx['name'], '/contexts/reservation-info');
    //     });

    //     if (!$reservationContext) {
    //         return response()->json([
    //             'fulfillmentText' => 'Xin lỗi, có vẻ như đã xảy ra lỗi trong việc duy trì thông tin đặt bàn. Vui lòng thử lại.'
    //         ]);
    //     }

    //     $params = $reservationContext['parameters'] ?? [];
    //     $guestName = is_array($params['guest_name'] ?? null) && !empty($params['guest_name']) ? $params['guest_name'][0] : ($params['guest_name'] ?? null);
    //     $guestPhone = is_array($params['guest_phone'] ?? null) && !empty($params['guest_phone']) ? $params['guest_phone'][0] : ($params['guest_phone'] ?? null);
    //     $guestEmail = is_array($params['guest_email'] ?? null) && !empty($params['guest_email']) ? $params['guest_email'][0] : ($params['guest_email'] ?? null);
    //     $numberOfGuests = is_array($params['guest_count'] ?? null) && !empty($params['guest_count']) ? $params['guest_count'][0] : ($params['guest_count'] ?? null);

    //     if (preg_match('/^\d+$/', $guestName) || strlen(trim($guestName)) < 2) {
    //         return response()->json([
    //             'fulfillmentText' => 'Tên không hợp lệ. Vui lòng nhập lại tên (chỉ gồm chữ cái).'
    //         ]);
    //     }

    //     if (!preg_match('/^(0|\+84)(3[2-9]|5[689]|7[06789]|8[1-5]|9[0-9])[0-9]{7}$/', $guestPhone)) {
    //         return response()->json([
    //             'fulfillmentText' => 'Số điện thoại không hợp lệ. Bạn vui lòng nhập số gồm 10 chữ số, bắt đầu bằng 0 hoặc +84 nhé!'
    //         ]);
    //     }

    //     if (!filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
    //         return response()->json([
    //             'fulfillmentText' => 'Email không hợp lệ. Bạn vui lòng nhập đúng định dạng email (vd: abc@gmail.com) nhé!'
    //         ]);
    //     }

    //     if (!is_numeric($numberOfGuests) || $numberOfGuests < 1 || $numberOfGuests > 20) {
    //         return response()->json([
    //             'fulfillmentText' => 'Số lượng người không hợp lệ. Bạn vui lòng nhập số từ 1 đến 20 nhé!'
    //         ]);
    //     }


    //     $time = null;
    //     if (isset($params['reservation_time'])) {
    //         if (is_array($params['reservation_time']) && !empty($params['reservation_time'])) {
    //             $time = $params['reservation_time'][0];
    //         } else if (is_string($params['reservation_time'])) {
    //             $time = $params['reservation_time'];
    //         }
    //     }

    //     if ($time) {
    //         $timeObj = new DateTime($time);
    //         $hour = (int)$timeObj->format('H');
    //         $minute = (int)$timeObj->format('i');

    //         $isValidTime = ($hour >= 8 && ($hour < 21 || ($hour === 21 && $minute <= 30)));

    //         if (!$isValidTime) {
    //             return response()->json([
    //                 'fulfillmentText' => '⏰ Thời gian phục vụ chỉ từ 08:00 đến 21:30. Vui lòng chọn lại thời gian đặt bàn phù hợp.'
    //             ]);
    //         }
    //     }

    //     $date = null;
    //     if (isset($params['reservation_date'])) {
    //         if (is_array($params['reservation_date']) && !empty($params['reservation_date'])) {
    //             $date = $params['reservation_date'][0];
    //         } else if (is_string($params['reservation_date'])) {
    //             $date = $params['reservation_date'];
    //         }
    //     }

    //     if ($date) {
    //         $dateObj = new DateTime($date);
    //         $today = new DateTime('today');

    //         if ($dateObj < $today) {
    //             return response()->json([
    //                 'fulfillmentText' => '⛔ Bạn không thể đặt bàn ở thời điểm đã qua. Vui lòng chọn thời gian trong tương lai.'
    //             ]);
    //         }
    //     }
    // }

    public function ngayDatQuickResponse(Request $request)
    {
        $params = $this->getReservationParams($request);
        if (!$params) {
            return response()->json([
                'fulfillmentText' => 'Không lấy được thông tin ngày đặt. Vui lòng thử lại.'
            ]);
        }

        $dateTime = $params['reservation_date'] ?? null;
        if (is_array($dateTime) && isset($dateTime['date_time'])) {
            $dateTime = $dateTime['date_time'];
        }

        if ($dateTime) {
            $dt = new DateTime($dateTime);
            $formatted = $dt->format('d/m/Y H:i');

            return response()->json([
                'fulfillmentText' => "Mình đã nhận được ngày đặt: $formatted. Đang xử lý thông tin còn lại..."
            ]);
        }

        return response()->json([
            'fulfillmentText' => 'Bạn vui lòng nhập lại ngày đặt nhé.'
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

        $guestName = $params['guest_name'] ?? '';
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

        $newContextParameters = $reservationContext['parameters'] ?? [];
        $newContextParameters['order_id'] = (string) $order->id;
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
                            "✅ Đặt bàn thành công! Mã đặt bàn của bạn là: " . $order->id . ". Hẹn gặp bạn lúc " . (new DateTime($from))->format('H:i d/m/Y') . ". Bạn có muốn chọn trước món không?"
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
                                        ['text' => '✅ Có', 'postback' => 'Có chọn món'], // Thêm postback để Dialogflow dễ nhận diện intent
                                        ['text' => '❌ Không', 'postback' => 'Không chọn món'], // Thêm postback
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            'outputContexts' => [
                [
                    'name' => $request->input('session') . '/contexts/reservation-info',
                    'lifespanCount' => 5,
                    'parameters' => $newContextParameters,
                ],
            ]
        ]);
        $response->send();
        Log::info('paramsss: ' . json_encode($reservationContext['parameters']));
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


    public function updateOrderDetails(Request $request)
    {
        try {
            $order_id = $request->order_id;
            $order = Order::find($order_id);
            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'K có đơn hàng.'
                ], 404);
            }
            // foreach ($order->details as $detail) {
            //     $detail->toppings()->delete();
            // }
            // $order->details()->delete();

            $newDetailsData = $request->input('details', []);
            $totalOrderPrice = 0;
            foreach ($newDetailsData as $detailData) {
                $foodId = $detailData['food_id'] ?? null;
                $comboId = $detailData['combo_id'] ?? null;
                $itemType = $detailData['type'];
                $quantity = $detailData['quantity'];

                $baseItemPrice = 0;
                // nếu là món ăn, tìm food và lấy giá bán
                if ($itemType === 'food' && $foodId) {
                    $food = Food::find($foodId);
                    $baseItemPrice = $food->sale_price ?? $food->price;
                } elseif ($itemType === 'combo' && $comboId) {
                    $combo = Combo::find($comboId);
                    $baseItemPrice = $combo->sale_price ?? $combo->price;
                } else {
                    return response()->json([
                        'status' => 'kh có food',
                    ], 404);
                }

                $orderDetail = new Order_detail([
                    'order_id' => $order->id,
                    'food_id' => $foodId,
                    'combo_id' => $comboId,
                    'quantity' => $quantity,
                    'type' => $itemType,
                    'price' => 0, // giá sẽ được tính sau khi tính cả topping
                ]);
                $orderDetail->save();

                $currentDetailTotalPrice = $baseItemPrice; // giá ban đầu của chi tiết (chưa gồm topping)
                $toppingPriceSum = 0; // tổng giá topping


                foreach ($detailData['toppings'] ?? [] as $toppingData) {
                    $foodTopping = Food_topping::find($toppingData['food_toppings_id']);
                    if ($foodTopping) {
                        $orderTopping = new Order_topping([
                            'order_detail_id' => $orderDetail->id,
                            'food_toppings_id' => $toppingData['food_toppings_id'],
                            'price' => $foodTopping->price, // lấy giá của topping từ food_toppings
                        ]);
                        $orderTopping->save();
                        $toppingPriceSum += $foodTopping->price; // cộng dồn giá topping
                    }
                }
                // tổng giá của chi tiết món ăn (giá gốc + tổng giá topping)
                $orderDetail->price = ($baseItemPrice + $toppingPriceSum);
                $orderDetail->save();

                // cộng dồn tổng giá của toàn bộ đơn hàng
                $totalOrderPrice += ($orderDetail->price * $quantity);
            }
            $order->total_price = $totalOrderPrice;
            $order->save();


            return response()->json([
                'status' => true,
                'message' => 'Cập nhật đơn hàng thành công',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function cancelReservationByChatBot(Request $request)
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

    public function getProductsForCarousel(Request $request)
    {
        $products = Food::all();
        $carouselCards = [];
        foreach ($products as $product) {
            $productId = (string) $product->id;;
            $carouselCards[] = [
                'title' => $product->name,
                'id' => $productId,
                'type' => 'food',
                'subtitle' => number_format($product->price, 0, ',', '.') . ' VNĐ',
                'buttons' => [
                    [
                        'text' => 'Chọn món này',
                    ]
                ]
            ];
        }

        if (empty($carouselCards)) {
            return response()->json([
                'fulfillmentMessages' => [
                    [
                        'text' => [
                            'text' => ['Hiện tại không có món nào để hiển thị. Vui lòng thử lại sau!']
                        ]
                    ]
                ]
            ]);
        }
        Log::info('product: ' . json_encode($carouselCards));
        return response()->json([
            'fulfillmentMessages' => [
                [
                    'text' => [
                        'text' => ['Đây là các món ăn hiện có của chúng tôi:']
                    ]
                ],
                [
                    'payload' => [
                        'richContent' => [
                            [
                                [
                                    'type' => 'carousel',
                                    'cards' => $carouselCards
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function combine(Request $request)
    {
        $intent = $request->input('queryResult.intent.displayName');
        Log::info('Webhook intent: ' . $intent);

        if ($intent === 'NgayDat') {
            return $this->InfoReservationByChatBot($request);
        } elseif ($intent === 'XacNhan') {
            return $this->ReservationByChatBot($request);
        } elseif ($intent === 'XacNhan - yes') {
            return $this->getProductsForCarousel($request);
        } elseif ($intent === 'ChonMon') {
            return $this->getProductsForCarousel($request);
        } elseif ($intent === 'ChonMon - yes') {
            return $this->updateOrderDetails($request);

            // } elseif ($intent === 'SoDienThoai') {
            //     return $this->checkGuestPhone($request);
            // } elseif ($intent === 'Email') {
            //     return $this->checkGuestEmail($request);
            // } elseif ($intent === 'SoLuongNguoi') {
            //     return $this->checkGuestCount($request);

        } elseif ($intent === 'HuyDon') {
            return $this->cancelReservationByChatBot($request);
        } else {
            return response()->json([
                'fulfillmentText' => 'Xin lỗi, tôi không hiểu yêu cầu của bạn.'
            ]);
        }
    }
}
