<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Jobs\SendReservationMail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\ReservationMail;
use App\Models\Combo;
use App\Models\Order;
use App\Models\Order_detail;
use App\Models\Order_topping;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Food;
use App\Models\Food_topping;
use App\Models\Payment;
use App\Models\Reservation_table;
use App\Models\Table;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

Carbon::setLocale('vi');
date_default_timezone_set('Asia/Ho_Chi_Minh');

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\error;

class OrderController extends Controller
{

    //tìm bàn
    public function getAvailableTables(Request $request)
    {
        $from = $request->input('reserved_from');
        $to = (new DateTime($from))->modify('+2 hours')->format('Y-m-d H:i:s');
        // $to = $request->input('reserved_to');

        $numberOfGuests = $request->input('number_of_guests');
        if ($numberOfGuests) {
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
                $allAvailableTables = Table::whereNotIn('id', $conflictingTableIds)
                    ->orderBy('table_number', 'asc')
                    ->get();

                $grouped = $allAvailableTables->sortBy('table_number')->values();
                $combinedGroup = [];
                $tempGroup = [];
                $tempCapacity = 0;

                for ($i = 0; $i < $grouped->count(); $i++) {
                    $current = $grouped[$i];
                    $tempGroup[] = $current;
                    $tempCapacity += $current->capacity;

                    if (
                        $i + 1 < $grouped->count() &&
                        $grouped[$i + 1]->table_number == $current->table_number + 1
                    ) {
                        continue;
                    }

                    if ($tempCapacity >= $numberOfGuests) {
                        $combinedGroup = $tempGroup;
                        break;
                    }

                    $tempGroup = [];
                    $tempCapacity = 0;
                }

                if (!empty($combinedGroup)) {
                    return response()->json([
                        'status' => true,
                        'combinedGroup' => true,
                        'message' => 'Liên hệ để được ghép bàn.'
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'combinedGroup' => false,
                    'message' => 'Hiện tại đã hết bàn phù hợp với số lượng khách yêu cầu.'
                ]);
            }

            $minCapacity = $availableTables->first()->capacity;

            $priorityTables = $availableTables->filter(function ($table) use ($minCapacity) {
                return $table->capacity == $minCapacity;
            })->values();
        } else {
            $conflictingTableIds = DB::table('reservation_tables')
                ->join('orders', 'reservation_tables.order_id', '=', 'orders.id')
                ->whereNotIn('orders.order_status', ['Đã hủy', 'Hoàn Thành'])
                ->where('reserved_from', '<', $to)
                ->where('reserved_to', '>', $from)
                ->pluck('reservation_tables.table_id')
                ->toArray();

            $priorityTables = Table::whereNotIn('id', $conflictingTableIds)
                ->orderBy('table_number', 'asc')
                ->get();
        }
        return response()->json([
            'status' => true,
            'tables' => $priorityTables
        ]);
    }



    public function chooseTable(Request $request)
    {
        try {
            $orderTime = Carbon::now();

            $order = Order::create([
                'user_id' => $request->user_id ?? null,
                'guest_count' => $request->guest_count,
                'order_time' => $orderTime,
                'expiration_time' => $orderTime->copy()->addMinutes(5),
            ]);

            $reserved_from = $request->reserved_from;
            $reserved_to = date('Y-m-d H:i:s', strtotime($reserved_from . ' +2 hours'));

            Reservation_table::create([
                'order_id' => $order->id,
                'table_id' => $request->table_id,
                'reserved_from' => $reserved_from,
                'reserved_to' => $reserved_to,
            ]);

            $table = Table::find($request->table_id);
            $table->update([
                'status' => 'Đã đặt trước',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Đã giữ bàn thành công.',
                'order_id' => $order->id
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    private function generateReservationCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Order::where('reservation_code', $code)->exists());

        return $code;
    }

    // đặt bàn
    public function reservation(Request $request)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'guest_name'  => 'required|string',
                    'guest_phone' => ['required', 'regex:/^0[0-9]{9}$/'],
                    'guest_email' => 'required|email',
                ],
                [
                    'guest_name.required'  => 'Tên khách hàng là bắt buộc.',
                    'guest_phone.required' => 'Số điện thoại là bắt buộc.',
                    'guest_phone.regex'    => 'Số điện thoại phải có 10 chữ số và bắt đầu bằng số 0.',
                    'guest_email.required' => 'Email là bắt buộc.',
                    'guest_email.email'    => 'Email không đúng định dạng.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $guestName = $request->guest_name ?? null;
            $guestPhone = $request->guest_phone ?? null;
            $guestEmail = $request->guest_email ?? null;

            if ($request->user_id) {
                $user = User::find($request->user_id);

                if ($user) {
                    $guestName = $user->fullname ?? $user->username;
                    $guestPhone = $user->phone;
                    $guestEmail = $user->email;


                    if ($request->filled('guest_name')) {
                        $guestName = $request->guest_name;
                    }

                    if ($request->filled('guest_phone')) {
                        $guestPhone = $request->guest_phone;
                    }
                    if ($request->filled('guest_email')) {
                        $guestEmail = $request->guest_email;
                    }
                }
            }

            if ($request->id) {
                $order = Order::find($request->id);
                if (!$order) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Đơn hàng không tồn tại.'
                    ], 404);
                }

                if (now()->gt($order->expiration_time)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Đơn hàng đã hết thời gian giữ bàn. Vui lòng đặt lại.'
                    ], 410);
                }

                $order->update([
                    'discount_id' => $request->discount_id ?? null,
                    'guest_name' => $guestName,
                    'guest_phone' => $guestPhone,
                    'guest_email' => $guestEmail,
                    'note' => $request->note ?? null,
                    'total_price' => $request->total_price ?? null,
                    'money_reduce' => $request->money_reduce ?? null,
                    'table_fee' => $request->table_fee,
                    'order_status' => 'Đã xác nhận',
                    'reservation_code' => $this->generateReservationCode(),
                ]);
            } else {
                $orderTime = Carbon::now();
                $order = Order::create([
                    'user_id' => $request->user_id ?? null,
                    'guest_count' => $request->guest_count,
                    'order_time' => $orderTime,
                    'expiration_time' => $orderTime->copy()->addMinutes(5),
                    'discount_id' => $request->discount_id ?? null,
                    'guest_name' => $guestName,
                    'guest_phone' => $guestPhone,
                    'guest_email' => $guestEmail,
                    'note' => $request->note ?? null,
                    'total_price' => $request->total_price ?? null,
                    'money_reduce' => $request->money_reduce ?? null,
                    'table_fee' => $request->table_fee ?? 0,
                    'order_status' => 'Đã xác nhận',
                    'reservation_code' => $this->generateReservationCode(),
                ]);

                $reserved_from = $request->reserved_from;
                $reserved_to = date('Y-m-d H:i:s', strtotime($reserved_from . ' +2 hours'));
                $createdTables = [];

                foreach ($request->table_ids as $table_id) {
                    $existing = Reservation_table::where('order_id', $order->id)
                        ->where('table_id', $table_id)
                        ->exists();

                    if ($existing) {
                        return response()->json([
                            'status' => false,
                            'message' => "Bàn số {$table_id} đã được chọn cho đơn hàng này rồi."
                        ], 400);
                    }

                    $reservation = Reservation_table::create([
                        'order_id' => $order->id,
                        'table_id' => $table_id,
                        'reserved_from' => $reserved_from,
                        'reserved_to' => $reserved_to,
                    ]);
                    $createdTables[] = $reservation;
                }
            }

            // Xử lý order_detail và order_topping
            $orderDetailsWithNames = [];

            if (!empty($request->order_detail)) {
                foreach ($request->order_detail as $item) {
                    $orderDetail = Order_detail::create([
                        'order_id' => $order->id,
                        'food_id' => $item['food_id'] ?? null,
                        'combo_id' => $item['combo_id'] ?? null,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'type' => $item['type'],
                    ]);

                    if (!empty($item['toppings'])) {
                        foreach ($item['toppings'] as $topping) {
                            Order_topping::create([
                                'order_detail_id' => $orderDetail->id,
                                'food_toppings_id' => $topping['food_toppings_id'],
                                'price' => $topping['price']
                            ]);
                        }
                    }
                }
            }

            //     foreach ($request->order_detail as $item) {
            //         $name = null;
            //         if ($item['type'] === 'food' && !empty($item['food_id'])) {
            //             $food = Food::find($item['food_id']);
            //             $name = $food?->name ?? 'Món ăn không tồn tại';
            //             $image = $food?->image;
            //         }
            //         if ($item['type'] === 'combo' && !empty($item['combo_id'])) {
            //             $combo = Combo::find($item['combo_id']);
            //             $name = $combo?->name ?? 'Món ăn không tồn tại';
            //             $image = $combo?->image;
            //         }

            //         $toppingsWithNames = [];
            //         if (!empty($item['toppings'])) {
            //             foreach ($item['toppings'] as $topping) {
            //                 $foodToppingModel = Food_topping::find($topping['food_toppings_id']);
            //                 $toppingModel = $foodToppingModel?->toppings;

            //                 $toppingsWithNames[] = [
            //                     'name' => $toppingModel?->name ?? 'Topping không tồn tại',
            //                     'price' => $topping['price']
            //                 ];
            //             }
            //         }

            //         $orderDetailsWithNames[] = [
            //             'name' => $name,
            //             'image' => $image,
            //             'quantity' => $item['quantity'],
            //             'price' => $item['price'],
            //             'type' => $item['type'],
            //             'toppings' => $toppingsWithNames,
            //         ];
            //     }
            // }
            // $subtotal = 0;

            // foreach ($orderDetailsWithNames as $item) {
            //     $itemSubtotal = $item['price'] * $item['quantity'];
            //     if (!empty($item['toppings'])) {
            //         foreach ($item['toppings'] as $topping) {
            //             $itemSubtotal += $topping['price'] * $item['quantity'];
            //         }
            //     }

            //     $subtotal += $itemSubtotal;
            // }

            // $tableInfos = $order->tables->map(function ($table) {
            //     return [
            //         'table_number'  => $table->table_number ?? 'Không rõ',
            //         'reserved_from' => $table->pivot->reserved_from,
            //         'reserved_to'   => $table->pivot->reserved_to,
            //     ];
            // })->toArray();

            // $qrImage = QrCode::format('png')->size(250)->generate('http://localhost:5173/history-order-detail/' . $order->id);

            // $filename = 'qr_' . $order->id . '.png';
            // $tempPath = storage_path('app/public/' . $filename);
            // file_put_contents($tempPath, $qrImage);

            // $uploadedFileUrl = Cloudinary::upload($tempPath, [
            //     'folder' => 'qr_codes'
            // ])->getSecurePath();

            // unlink($tempPath);

            // $mailData = [
            //     'order_id' => $order->id,
            //     'reservation_code' => $order->reservation_code,
            //     'guest_name' => $guestName,
            //     'guest_email' => $guestEmail,
            //     'guest_phone' => $guestPhone,
            //     'guest_count' => $request->guest_count || $order->guest_count,
            //     'total_price' => $request->total_price ?? null,
            //     'note' => $request->note ?? null,
            //     'order_details' => $orderDetailsWithNames,
            //     'tables' => $tableInfos,
            //     'subtotal' => $subtotal,
            //     'order_status' =>  $order->order_status,
            //     'qr_url' => $uploadedFileUrl
            // ];


            // Mail::to($mailData['guest_email'])->send(new ReservationMail($mailData));

            return response()->json([
                'status' => true,
                'message' => 'Đặt bàn thành công.',
                'order_id' => $order->id
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->getMessage()
            ], 422);
        }
    }


    // đặt bàn nhanh
    public function makeReservationQuickly(Request $request)
    {

        $from = $request->input('reserved_from');
        $to = (new DateTime($from))->modify('+2 hours')->format('Y-m-d H:i:s');
        $numberOfGuests = $request->input('number_of_guests');
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
                'mess' => 'Xin lỗi, hiện tại không còn bàn trống phù hợp. Bạn thử lại thời gian khác hoặc liên hệ để được hỗ trợ nhé!.'
            ], 404);
        }
        $orderTime = Carbon::now();
        $reserved_to = date('Y-m-d H:i:s', strtotime($from . ' +2 hours'));

        $order = Order::create([
            'guest_count' => $numberOfGuests,
            'order_time' => $orderTime,
            'expiration_time' => $orderTime->copy()->addMinutes(5),
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

        return response()->json([
            'message' => 'Tìm bàn thành công',
            'orderId' => $order->id
        ]);
    }


    //load món đã đặt
    public function showOrderDetail($order_id)
    {
        try {
            $orderDetail = Order::with([
                'details.foods',
                'details.toppings.food_toppings.toppings'
            ])->find($order_id);

            return response()->json([
                'status' => 'success',
                'data' => $orderDetail
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    //thêm món
    public function updateOrderDetails(Request $request, $order_id)
    {
        try {
            $order = Order::find($order_id);
            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'K có đơn hàng.'
                ], 404);
            }
            foreach ($order->details as $detail) {
                $detail->toppings()->delete();
            }
            $order->details()->delete();


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


    //chuyển bàn
    public function changeTable(Request $request)
    {
        try {
            $updated = Reservation_table::where('order_id', $request->id)
                ->update(['table_id' => $request->table_id]);

            if ($updated) {
                return response()->json('Chuyển bàn thành công');
            } else {
                return response()->json([
                    'status' => false,
                    'errors' => 'Không tìm thấy đặt bàn phù hợp'
                ], 404);
            }
        } catch (Exception $th) {
            return response()->json([
                'status' => false,
                'errors' => $th->getMessage()
            ], 422);
        }
    }

    // lấy thông tin theo id đơn hàng hoặc id user
    public function getInfoReservation(Request $request)
    {
        $value = $request->query('value');
        $type = $request->query('type');

        if ($type === 'user') {
            $reservation = Order::with([
                'details.foods',
                'details.combos',
                'details.toppings.food_toppings.toppings',
                'tables',
                'payment',
            ])->where('user_id', $value)->orderBy('id', 'desc')->first();
        } else {
            $reservation = Order::with([
                'details.foods',
                'details.toppings.food_toppings.toppings',
                'tables',
                'payment',
            ])->find($value);
        }

        if (!$reservation) {
            return response()->json([
                'status' => false,
                'mess' => 'Không tìm thấy đơn đặt bàn.',
            ], 404);
        }

        $details = $reservation->details->map(function ($detail) {
            // $foodName = null;
            // $image = null;
            $image = null;
            $item_id = null;
            $nameKey = null;
            $nameValue = null;

            if ($detail->type === 'food') {
                $item_id = $detail->food_id;
                $nameKey = 'food_name';
                $nameValue = optional($detail->foods)->name;
                $image = optional($detail->foods)->image;
            } elseif ($detail->type === 'combo') {
                $item_id = $detail->combo_id;
                $nameKey = 'combo_name';
                $nameValue = optional($detail->combos)->name;
                $image = optional($detail->combos)->image;
            }

            // return [
            //     'id' => $detail->id,
            //     'food_id' => $detail->food_id,
            //     'food_name' => $detail->foods->name ?? null,
            //     'quantity' => $detail->quantity,
            //     'price' => $detail->price,
            //     'image' => $detail->foods->image ?? null,
            //     'type' => $detail->type,

            //     'toppings' => $detail->toppings->map(function ($toppings) {
            //         return [
            //             'food_toppings_id' => $toppings->food_toppings_id,
            //             'topping_name' => $toppings->food_toppings->toppings->name ?? null,
            //             'price' => $toppings->price
            //         ];
            //     })
            // ];
            return array_merge([
                'id' => $detail->id,
                'item_id' => $item_id,
                'quantity' => $detail->quantity,
                'price' => $detail->price,
                'image' => $image,
                'type' => $detail->type,
                'toppings' => $detail->toppings->map(function ($toppings) {
                    return [
                        'food_toppings_id' => $toppings->food_toppings_id,
                        'topping_name' => optional(optional($toppings->food_toppings)->toppings)->name,
                        'price' => $toppings->price
                    ];
                })
            ], [
                $nameKey => $nameValue // thêm key động: 'food_name' hoặc 'combo_name'
            ]);
        });

        $tables = $reservation->tables->map(function ($table) {
            return [
                'table_id' => $table->id,
                'table_number' => $table->table_number,
                'reserved_from' => $table->pivot->reserved_from,
                'reserved_to' => $table->pivot->reserved_to,
                'reservation_status' => $table->pivot->reservation_status,
            ];
        });
        $paymentInfo = $reservation->payment->map(function ($p) {
            return [
                'payment_id' => $p->id,
                'payment_method' => $p->payment_method,
                'payment_status' => $p->payment_status,
                'amount_paid' => $p->amount_paid,
                'payment_time' => $p->payment_time,
            ];
        });

        return response()->json([
            'status' => true,
            'mess' => 'Lấy thông tin thành công',
            'info' => [
                'id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'shipper_id' => $reservation->shipper_id,
                'discount_id' => $reservation->discount_id,
                'order_time' => $reservation->order_time,
                'order_status' => $reservation->order_status,
                'total_price' => $reservation->total_price,
                'tpoint_used' => $reservation->tpoint_used,
                'ship_cost' => $reservation->ship_cost,
                'table_fee' => $reservation->table_fee,
                'comment' => $reservation->comment,
                'review_time' => $reservation->review_time,
                'rating' => $reservation->rating,
                'guest_name' => $reservation->guest_name,
                'guest_phone' => $reservation->guest_phone,
                'guest_email' => $reservation->guest_email,
                'guest_address' => $reservation->guest_address,
                'guest_count' => $reservation->guest_count,
                'note' => $reservation->note,
                'money_reduce' => $reservation->money_reduce,
                'check_in_time' => $reservation->check_in_time,
                'reservations_time' => $reservation->reservations_time,
                'expiration_time' => $reservation->expiration_time,
                'details' => $details,
                'tables' => $tables,
                'payment_info' => $paymentInfo,
                'total_paid' => $reservation->payment->sum('amount_paid'),
            ]
        ], 200);
    }
    public function getOrderReservationInfo(Request $request)
    {
        $type = $request->input('type');
        $value = $request->input('value');

        if ($type === 'user_id') {
            $orders = Order::where('user_id', $value)->latest()->take(1)->get();
        } else if ($type === 'order_id') {
            $orders = Order::where('id', $value)->get();
        } else {
            return response()->json(['orders' => []]);
        }

        return response()->json(['orders' => $orders]);
    }

    //lấy tất cả order theo user
    public function getInfoOrderByUser(Request $request)
    {
        $userId = $request->user()->id;

        $orders = Order::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    /**huy don cho guess */
    public function lookup(Request $request)
    {
        $request->validate([
            'phone' => ['nullable','string','min:4','max:20'],
            'code'  => ['nullable','string','max:64'],
            'limit' => ['nullable','integer','min:1','max:5'],
        ]);
    
        if (!$request->filled('phone') && !$request->filled('code')) {
            return response()->json(['message' => 'Vui lòng nhập SĐT hoặc mã đơn.'], 422);
        }
    
        $q = Order::query()->select(['id','order_code','order_status','ship_cost','total_price', 'order_time'])
        ->orderByDesc('order_time');
    
        if ($request->filled('phone')) {
            $q->where('guest_phone', 'like', '%'.$request->input('phone').'%');
        }
        if ($request->filled('code')) {
            $q->where('order_code', $request->input('code'));
            $q->with([
                'details:id,order_id,food_id,combo_id,quantity,price,is_flash_sale',
                'details.foods:id,name,image',
                'details.combos:id,name,image',
            ]);
        } else {
            $q->limit($request->integer('limit', 5));
        }
    
        return OrderResource::collection($q->get());
    }
    
    public function show(Request $request, string $code)
    {
        $q = Order::query()
          ->select(['id','order_code','order_status','ship_cost','total_price','order_time'])
          ->where('order_code', $code)
          ->orderByDesc('order_time');
    
        if ($request->filled('phone')) {
            $q->where('guest_phone', 'like', '%'.$request->input('phone').'%');
        }
    
        $q->with([
            'details:id,order_id,food_id,combo_id,quantity,price,is_flash_sale',
            'details.foods:id,name,image',
            'details.combos:id,name,image',
        ]);
    
        $order = $q->first();
    
        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn.'], 404);
        }
    
        return new OrderResource($order);
    }
    
    public function cancelByConfirm(Request $request, string $code)
    {
        $data = $request->validate([
            'confirm' => ['required','string','max:32'],
            'reason'  => ['nullable','string','max:255'],
        ]);
    
        $confirm = mb_strtolower(trim($data['confirm']));
        if ($confirm !== 'xacnhan') {
            return response()->json(['message' => 'Vui lòng gõ đúng "xacnhan" để huỷ đơn.'], 422);
        }
        $order = Order::query()
            ->where('order_code', $code)
            ->with([
                'details:id,order_id,food_id,combo_id,quantity,price,is_flash_sale',
                'details.foods:id', 
            ])
            ->first();
    
        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn.'], 404);
        }
    
        $status = trim((string) $order->order_status);
        $cancellable = ['Chờ xác nhận', 'Đã xác nhận'];
        if (!in_array($status, $cancellable, true)) {
            return response()->json(['message' => 'Đơn không thể huỷ ở trạng thái hiện tại.'], 400);
        }
    
        DB::transaction(function () use ($order, $data) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            foreach ($order->details as $detail) {
                if ($detail->food_id) {
                    $food = Food::whereKey($detail->food_id)->lockForUpdate()->first();
                    if ($food) {
                        $qty = (int) $detail->quantity;
    
                        if ((bool) $detail->is_flash_sale) {
                            $food->flash_sale_quantity = (int) $food->flash_sale_quantity + $qty;
                            $newSold = (int) $food->flash_sale_sold - $qty;
                            $food->flash_sale_sold = $newSold > 0 ? $newSold : 0;
                        } else {
                            $food->stock = (int) $food->stock + $qty;
                            $newSold = (int) $food->quantity_sold - $qty;
                            $food->quantity_sold = $newSold > 0 ? $newSold : 0;
                        }

                        $food->save();
                    }
                }
            }
            $order->order_status = 'Đã huỷ';
            if (Schema::hasColumn($order->getTable(), 'canceled_at')) {
                $order->canceled_at = now();
            }
            if (Schema::hasColumn($order->getTable(), 'cancel_reason')) {
                $order->cancel_reason = $data['reason'] ?? 'Khách gõ "xacnhan" huỷ';
            }
    
            $order->save();
        });
        $order->load([
            'details:id,order_id,food_id,combo_id,quantity,price,is_flash_sale',
            'details.foods:id,name,image',
            'details.combos:id,name,image',
        ]);
    
        return new OrderResource($order);
    }

    //huỷ đơn
    public function cancelOrder(Request $request)
    {
        $order = Order::with(['payment', 'details'])->find($request->id);

        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng.'], 404);
        }

        if (!in_array($order->order_status, ['Chờ xác nhận', 'Đã xác nhận'])) {
            return response()->json(['message' => 'Chỉ có thể hủy đơn khi đang ở trạng thái chờ xác nhận hoặc đã xác nhận.'], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($order->details as $detail) {
                $food = Food::find($detail->food_id);
                if ($food) {
                    if ($detail->is_flash_sale) {
                        $food->flash_sale_quantity += $detail->quantity;
                        $food->flash_sale_sold -= $detail->quantity;
                    } else {
                        $food->stock += $detail->quantity;
                        $food->quantity_sold -= $detail->quantity;
                    }
                    $food->save();
                }
            }
            /** restone tpoint */
            if ($order->user_id && $order->tpoint_used > 0) {
                $user = User::find($order->user_id);
                if ($user) {
                    $user->usable_points += $order->tpoint_used;
                    $user->save();
                }
            }

            // Cập nhật trạng thái đơn hàng
            $order->order_status = 'Đã hủy';
            $order->save();

            if ($order->payment) {
                if (in_array($order->payment->payment_method, ['VNPAY', 'MOMO'])) {
                    $order->payment->payment_status = 'Đã hoàn tiền';
                } else {
                    $order->payment->payment_status = 'Thanh toán thất bại';
                }
                $order->payment->save();
            }

            DB::commit();
            return response()->json(['message' => 'Đơn hàng đã được hủy thành công.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi hủy đơn hàng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    //cập nhật địa chỉ
    public function updateAddressForOrder(Request $request, $id)
    {
        try {
            $request->validate([
                'guest_address' => 'required|string|max:255',
            ]);
            $order = Order::find($id);
            if (!$order) {
                return response()->json(['message' => 'Không tìm thấy đơn hàng.'], 404);
            }
            $order->guest_address = $request->guest_address;
            if ($order->save()) {
                return response()->json(['message' => 'Địa chỉ đã được thay đổi thành công.']);
            } else {
                return response()->json(['message' => 'Đã xảy ra lỗi khi cập nhật địa chỉ.'], 500);
            }
        } catch (ValidationException $th) {
            return response()->json([
                'status' => false,
                'errors' => $th->errors()
            ], 422);
        }
    }


    //lấy đơn đặt bàn
    public function getOrderOfTable()
    {
        $reservations = Reservation_table::with([
            'order.details.foods',
            'order.details.toppings.food_toppings.toppings',
            'order.tables'
        ])->get();

        if ($reservations->isNotEmpty()) {
            $data = $reservations->map(function ($reservation) {
                $order = $reservation->order;
                $totalQuantity = $order->details->sum('quantity');
                $details = $order->details->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'food_id' => $detail->food_id,
                        'food_name' => $detail->food_name ?? null,
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'image' => $detail->foods->image ?? null,
                        'type' => $detail->type,
                        'toppings' => $detail->toppings->map(function ($topping) {
                            return [
                                'food_toppings_id' => $topping->food_toppings_id,
                                'topping_name' => $topping->food_toppings->topping->name ?? null,
                                'price' => $topping->price,
                            ];
                        }),
                    ];
                });

                return [
                    'reserved_from' => $reservation->reserved_from,
                    'reserved_to' => $reservation->reserved_to,

                    // các thông tin order
                    'id' => $order->id,
                    'user_id' => $order->user_id,
                    'discount_id' => $order->discount_id,
                    'order_time' => $order->order_time,
                    'order_status' => $order->order_status,
                    'total_price' => $order->total_price,
                    'comment' => $order->comment,
                    'review_time' => $order->review_time,
                    'rating' => $order->rating,
                    'guest_name' => $order->guest_name,
                    'guest_phone' => $order->guest_phone,
                    'guest_email' => $order->guest_email,
                    'guest_address' => $order->guest_address,
                    'guest_count' => $order->guest_count,
                    'note' => $order->note,
                    'check_in_time' => $order->check_in_time,
                    'expiration_time' => $order->expiration_time,
                    'money_reduce' => $order->money_reduce,
                    'details' => $details,
                    'total_quantity' => $totalQuantity,
                    'tables' => $order->tables->map(function ($table) {
                        return [
                            'id' => $table->id,
                            'table_number' => $table->table_number,
                            'capacity' => $table->capacity,
                            'status' => $table->status
                        ];
                    }),
                ];
            });

            return response()->json([
                'status' => true,
                'mess' => 'Lấy danh sách đơn hàng thành công',
                'orders' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'mess' => 'Không có đơn hàng nào'
            ]);
        }
    }

    // lấy đơn hiện thời
    public function getCurrentOrder()
    {
        $reservations = Reservation_table::with([
            'order.details.foods',
            'order.details.toppings.food_toppings.toppings',
            'order.tables'
        ])->whereHas('order', function ($query) {
            $query->whereNull('guest_address');
        })->get();

        if ($reservations->isNotEmpty()) {
            $data = $reservations->map(function ($reservation) {
                $order = $reservation->order;
                $totalQuantity = $order->details->sum('quantity');
                $details = $order->details->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'food_id' => $detail->food_id,
                        'food_name' => $detail->foods->name ?? null, // Lấy tên món ăn từ mối quan hệ foods
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'toppings' => $detail->toppings->map(function ($topping) {
                            return [
                                'food_toppings_id' => $topping->food_toppings_id,
                                'topping_name' => $topping->food_toppings->toppings->name ?? null,
                                'price' => $topping->price,
                            ];
                        }),
                    ];
                });

                return [
                    'reserved_from' => $reservation->reserved_from,
                    'reserved_to' => $reservation->reserved_to,
                    'id' => $order->id,
                    'user_id' => $order->user_id,
                    'discount_id' => $order->discount_id,
                    'order_time' => $order->order_time,
                    'order_status' => $order->order_status,
                    'total_price' => $order->total_price,
                    'comment' => $order->comment,
                    'review_time' => $order->review_time,
                    'rating' => $order->rating,
                    'guest_name' => $order->guest_name,
                    'guest_phone' => $order->guest_phone,
                    'guest_email' => $order->guest_email,
                    'guest_address' => $order->guest_address,
                    'guest_count' => $order->guest_count,
                    'note' => $order->note,
                    'check_in_time' => $order->check_in_time,
                    'expiration_time' => $order->expiration_time,
                    'money_reduce' => $order->money_reduce,
                    'details' => $details,
                    // 'details1' => $order->details,
                    'total_quantity' => $totalQuantity,
                    'tables' => $order->tables->map(function ($table) {
                        return [
                            'id' => $table->id,
                            'table_number' => $table->table_number,
                            'capacity' => $table->capacity,
                            'status' => $table->status
                        ];
                    }),
                ];
            });

            $sortedData = $data->sort(function ($a, $b) {
                $statusA = $a['order_status'];
                $statusB = $b['order_status'];

                $timeA = ($statusA === 'Khách đã đến' && $a['check_in_time'])
                    ? Carbon::parse($a['check_in_time'])
                    : Carbon::parse($a['order_time']);

                $timeB = ($statusB === 'Khách đã đến' && $b['check_in_time'])
                    ? Carbon::parse($b['check_in_time'])
                    : Carbon::parse($b['order_time']);

                if ($statusA === 'Khách đã đến' && $statusB !== 'Khách đã đến') {
                    return -1;
                } elseif ($statusA !== 'Khách đã đến' && $statusB === 'Khách đã đến') {
                    return 1;
                } else {
                    return $timeA->timestamp - $timeB->timestamp;
                }
            })->values();

            return response()->json([
                'status' => true,
                'mess' => 'Lấy danh sách đơn hàng thành công',
                'orders' => $sortedData
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'mess' => 'Không có đơn hàng nào'
            ]);
        }
    }

    //xếp bàn
    public function setUpTable(Request $request)
    {
        try {
            $data = $request->validate([
                'order_id' => 'required|numeric',
                'table_ids' => 'required|array',
                'table_ids.*' => 'numeric',
                'reserved_from' => 'nullable|date',
                'reserved_to' => 'nullable|date',
            ], [
                'table_ids.required' => 'Bạn chưa xếp bàn cho đơn hàng này.',
            ]);

            if (empty($data['reserved_to'])) {
                $order = Order::find($data['order_id']);
                if (!$order) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Không tìm thấy đơn hàng.'
                    ], 404);
                }
                $data['reserved_to'] = $order->reserved_to;
            }

            $createdTables = [];

            foreach ($data['table_ids'] as $table_id) {
                $existing = Reservation_table::where('order_id', $data['order_id'])
                    ->where('table_id', $table_id)
                    ->exists();

                if ($existing) {
                    return response()->json([
                        'status' => false,
                        'message' => "Bàn số {$table_id} đã được chọn cho đơn hàng này rồi."
                    ], 400);
                }

                $reservation = Reservation_table::create([
                    'order_id' => $data['order_id'],
                    'table_id' => $table_id,
                    'reserved_from' => $data['reserved_from'],
                    'reserved_to' => $data['reserved_to'],
                ]);
                $createdTables[] = $reservation;
            }

            return response()->json([
                'status' => true,
                'message' => 'Xếp bàn thành công',
            ]);
        } catch (ValidationException $th) {
            return response()->json([
                'status' => false,
                'errors' => $th->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getAllFoodsWithToppings()
    {
        try {
            $foods = Food::with('toppings')->get();
            return response()->json($foods);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi lấy danh sách món ăn và topping',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //cập nhật trạng thái
    public function updateStatus(Request $request)
    {
        $order = Order::find($request->id);
        $reservation = Reservation_table::where('order_id', $request->id)->first();

        $order->order_status = $request->order_status;

        if ($request->order_status === 'Khách đã đến') {
            $order->check_in_time = Carbon::now();
        }

        if ($request->order_status === 'Hoàn thành') {
            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->payment_status = 'Đã thanh toán';
                $payment->save();
            }

            if ($reservation) {
                $reservation->reserved_to = Carbon::now();
                $reservation->save();
            }
        }

        $order->save();

        return response()->json([
            'status' => $request->order_status,
            'payment_status' => $order->payment_status,
            'message' => 'Đơn hàng đã được cập nhật thành công.'
        ]);
    }


    public function autoCancelOrders()
    {
        $now = now();
        $orders = Order::whereNull('check_in_time')
            ->where('expiration_time', '<', $now)
            ->where('reservation_status', '!=', 'Đã huỷ')
            ->get();

        foreach ($orders as $order) {
            $order->reservation_status = 'Đã huỷ';
            $order->save();
        }

        return response()->json([
            'message' => 'Đã huỷ ' . $orders->count() . ' đơn quá hạn chưa check-in.',
        ]);
    }

    //in hoá đơn
    public function generateInvoice(Request $request)
    {
        try {
            $order = Order::with([
                'details.foods',
                'details.toppings.food_toppings.toppings',
                'tables'
            ])->find($request->id);

            $orderDetailsWithNames = [];
            foreach ($order->details as $item) {
                $name = $item->foods->name ?? 'Món ăn không tồn tại';
                $toppingsWithNames = [];
                foreach ($item->toppings as $topping) {
                    $foodToppingModel = $topping->food_toppings;
                    $toppingModel = $foodToppingModel?->toppings;
                    $toppingsWithNames[] = [
                        'name' => $toppingModel?->name ?? 'Topping không tồn tại',
                        'price' => $topping->price
                    ];
                }
                $orderDetailsWithNames[] = [
                    'name' => $name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'type' => $item->type,
                    'toppings' => $toppingsWithNames,
                ];
            }

            $pdfData = [
                'order_id' => $order->id,
                'guest_name' => $order->guest_name,
                'guest_phone' => $order->guest_phone,
                'guest_email' => $order->guest_email,
                'total_price' => $order->total_price,
                'note' => $order->note,
                'order_details' => $orderDetailsWithNames,
                'tables' => $order->tables->pluck('table_number')->toArray(),
                'order_time' => $order->order_time,
                'reservations_time' => $order->reservations_time,
            ];

            // Tạo PDF
            $pdf = PDF::loadView('pdf.invoice', $pdfData);
            // return $pdf->download('invoice_' . $order->id . '.pdf');
            return $pdf->stream('hoadon' . $order->id . '.pdf', ['Attachment' => 0]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Lỗi khi tạo hóa đơn: ' . $e->getMessage()
            ], 500);
        }
    }

    //cập nhật mã giảm giá
    public function reservationUpdate(Request $request)
    {
        try {
            $order = Order::find($request->id);
            $data = $request->validate([
                'discount_id' => 'nullable|numeric',
            ]);

            $order->update([
                'discount_id' => $data['discount_id'] ?? null,
            ]);
            return response()->json(['order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }


    public function getOrderByUser($id)
    {
        $order = DB::table('orders')
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        return response()->json([
            'message' => 'Đã tìm thấy đơn hàng',
            'data' => $order
        ]);
    }


    public function getOrdersByShipper()
    {
        $shipper = auth()->user();

        if (!$shipper) {
            return response()->json([
                'status' => false,
                'mess' => 'Chưa xác thực người dùng'
            ], 401);
        }

        $orders = Order::with([
            'details.foods',
            'details.toppings.food_toppings.toppings',
            'tables',
            'payment'
        ])
            ->where('shipper_id', $shipper->id)
            ->whereIn('order_status', ['Bắt đầu giao', 'Đang giao hàng'])
            ->orderByDesc('order_time')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => false,
                'mess' => 'Không có đơn hàng nào được giao cho bạn'
            ]);
        }

        $data = $orders->map(function ($order) {
            $details = $order->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'food_id' => $detail->food_id,
                    'food_name' => optional($detail->foods)->name,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                    'image' => optional($detail->foods)->image,
                    'type' => $detail->type,
                    'toppings' => $detail->toppings->map(function ($toppings) {
                        return [
                            'food_toppings_id' => $toppings->food_toppings_id,
                            'topping_name' => $toppings->food_toppings->toppings->name ?? null,
                            'price' => $toppings->price,
                        ];
                    })
                ];
            });

            return [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'shipper_id' => $order->shipper_id,
                'discount_id' => $order->discount_id,
                'order_time' => $order->order_time,
                'order_status' => $order->order_status,
                'total_price' => $order->total_price,
                'comment' => $order->comment,
                'review_time' => $order->review_time,
                'rating' => $order->rating,
                'guest_name' => $order->guest_name,
                'guest_phone' => $order->guest_phone,
                'guest_email' => $order->guest_email,
                'guest_address' => $order->guest_address,
                'guest_count' => $order->guest_count,
                'note' => $order->note,
                'check_in_time' => $order->check_in_time,
                'expiration_time' => $order->expiration_time,
                'money_reduce' => $order->money_reduce,
                'type_order' => $order->type_order,
                'details' => $details,
                'tables' => $order->tables->map(function ($table) {
                    return [
                        'table_number' => $table->table_number,
                        'capacity' => $table->capacity,
                        'status' => $table->status,
                        'order_id' => $table->pivot->order_id,
                        'table_id' => $table->pivot->table_id,
                        'reservation_status' => $table->pivot->reservation_status,
                        'reserved_from' => $table->pivot->reserved_from,
                        'reserved_to' => $table->pivot->reserved_to,
                    ];
                }),
                'payment' => [
                    'amount_paid' => $order->payment->amount_paid ?? null,
                    'payment_method' => $order->payment->payment_method ?? null,
                    'payment_status' => $order->payment->payment_status ?? null,
                    'payment_time' => $order->payment->payment_time ?? null,
                    'payment_type' => $order->payment->payment_type ?? null,
                ],
            ];
        });

        return response()->json([
            'status' => true,
            'mess' => 'Lấy đơn hàng của shipper thành công',
            'orders' => $data
        ]);
    }

    public function assignShipper(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'shipper_id' => 'required|integer',
        ]);

        try {
            Order::whereIn('id', $request->order_ids)->update([
                'shipper_id' => $request->shipper_id,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Giao đơn hàng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getShipperOrders($id)
    {
        $orders = Order::where('shipper_id', $id)
            ->whereIn('order_status', ['Đang giao hàng', 'Bắt đầu giao'])
            ->get();

        return response()->json(['orders' => $orders]);
    }
}
