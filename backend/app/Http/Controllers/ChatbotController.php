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
use App\Jobs\SendReservationMailJob;
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
            // if($message === '✅ Xác nhận'){
            //     ProcessDialogflowMessage::dispatch($sessionId, $message);
            // }else{
            //     $dialogflowService = app(DialogflowService::class); // lấy instance từ service container của Laravel
            //     $job = new ProcessDialogflowMessage($sessionId, $message);
            //     $job->handle($dialogflowService);
            // }

                $dialogflowService = app(DialogflowService::class); // lấy instance từ service container của Laravel
                $job = new ProcessDialogflowMessage($sessionId, $message);
                $job->handle($dialogflowService);

            return response()->json(['status' => 'Tin nhắn đang được xử lý.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể xử lý yêu cầu.'], 500);
        }
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
        $reservationContext = collect($outputContexts)->first(fn($ctx) => str_ends_with($ctx['name'], '/contexts/reservation-info'));
        $params = $reservationContext['parameters'] ?? [];

        $guestName = $params['guest_name'] ?? '';
        $guestPhone = $params['guest_phone'] ?? '';
        $guestEmail = $params['guest_email'] ?? '';
        $numberOfGuests = $params['guest_count'] ?? null;
        $date = $params['reservation_date'] ?? '';
        $time = $params['reservation_time'] ?? '';

        if (!$time || !$numberOfGuests) {
            return response()->json([
                'fulfillmentMessages' => [[
                    'text' => ['text' => ['Thông tin thời gian hoặc số lượng khách không hợp lệ. Vui lòng thử lại.']]
                ]]
            ]);
        }

        $from = Carbon::parse($time);
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
                            "✅ Đặt bàn thành công! Mã đặt bàn của bạn là: {$order->id}. Hẹn gặp bạn lúc " . $from->format('H:i d/m/Y') . ". Bạn có muốn chọn trước món không?"
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
                                        ['text' => '✅ Có', 'postback' => 'Có chọn món'],
                                        ['text' => '❌ Không', 'postback' => 'Không chọn món'],
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

        // // Đẩy gửi mail vào job queue
        dispatch(new SendReservationMail(
            $order->id,
            // $guestName,
            // $guestEmail,
            // $guestPhone,
            // $numberOfGuests,
            // $request->total_price ?? null,
            // $request->note ?? null
        ));

        Log::info('Đã đẩy job gửi mail vào hàng đợi');

        return $response;
    }

    // private function findTablesGroup($availableTables, $requiredCapacity)
    // {
    //     // Tìm bàn đơn đủ chứa
    //     $singleTable = $availableTables->firstWhere('capacity', '>=', $requiredCapacity);
    //     if ($singleTable) {
    //         return collect([$singleTable]);
    //     }

    //     $selected = collect();
    //     $tempGroup = collect();
    //     $totalCap = 0;
    //     $prevTableNum = null;

    //     foreach ($availableTables as $table) {
    //         if ($prevTableNum === null || $table->table_number == $prevTableNum + 1) {
    //             $tempGroup->push($table);
    //             $totalCap += $table->capacity;
    //             $prevTableNum = $table->table_number;

    //             if ($totalCap >= $requiredCapacity) {
    //                 $selected = $tempGroup;
    //                 break;
    //             }
    //         } else {
    //             $tempGroup = collect([$table]);
    //             $totalCap = $table->capacity;
    //             $prevTableNum = $table->table_number;
    //         }
    //     }

    //     return $selected;
    // }



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
        } elseif ($intent === 'HuyDon') {
            return $this->cancelReservationByChatBot($request);
        } else {
            return response()->json([
                'fulfillmentText' => 'Xin lỗi, tôi không hiểu yêu cầu của bạn.'
            ]);
        }
    }
}

