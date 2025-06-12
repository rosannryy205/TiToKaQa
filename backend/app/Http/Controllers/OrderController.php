<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\Order;
use App\Models\Order_detail;
use App\Models\Order_topping;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Food;
use App\Models\Food_topping;
use App\Models\Reservation_table;
use App\Models\Table;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;

Carbon::setLocale('vi');
date_default_timezone_set('Asia/Ho_Chi_Minh');

use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

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
                ->get();

            if ($availableTables->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'tables' => []
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


    // đặt bàn
    public function reservation(Request $request)
    {
        try {

            $guestName = $request->guest_name ?? null;
            $guestPhone = $request->guest_phone ?? null;
            $guestEmail = $request->guest_email ?? null;

            if ($request->user_id) {
                $user = User::find($request->user_id);

                if ($user) {
                    $guestName = $user->fullname ?? $user->username;
                    $guestPhone = $user->phone;
                    $guestEmail = $user->email;


                    if ($request->filled('guest_name')) { // sử dụng `filled` để kiểm tra có tồn tại và không rỗng
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
                    'deposit_amount' => $request->deposit_amount ?? null,
                    'total_price' => $request->total_price ?? null,
                    'money_reduce' => $request->money_reduce ?? null,
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
                    'deposit_amount' => $request->deposit_amount ?? null,
                    'total_price' => $request->total_price ?? null,
                    'money_reduce' => $request->money_reduce ?? null,
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

                // Lấy tên món ăn và topping để gửi mail hoặc trả về
                foreach ($request->order_detail as $item) {
                    $name = null;
                    if ($item['type'] === 'food' && !empty($item['food_id'])) {
                        $food = Food::find($item['food_id']);
                        $name = $food?->name ?? 'Món ăn không tồn tại';
                    }

                    $toppingsWithNames = [];
                    if (!empty($item['toppings'])) {
                        foreach ($item['toppings'] as $topping) {
                            $foodToppingModel = Food_topping::find($topping['food_toppings_id']);
                            $toppingModel = $foodToppingModel?->toppings;

                            $toppingsWithNames[] = [
                                'name' => $toppingModel?->name ?? 'Topping không tồn tại',
                                'price' => $topping['price']
                            ];
                        }
                    }

                    $orderDetailsWithNames[] = [
                        'name' => $name,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'type' => $item['type'],
                        'toppings' => $toppingsWithNames,
                    ];
                }
            }

            // Chuẩn bị dữ liệu gửi mail
            $mailData = [
                'order_id' => $order->id,
                'guest_name' => $guestName,
                'guest_email' => $guestEmail,
                'guest_phone' => $guestPhone,
                'total_price' => $request->total_price ?? null,
                'note' => $request->note ?? null,
                'order_detail' => $orderDetailsWithNames,
            ];

            // Gửi mail nếu cần (bạn có thể bỏ comment và chỉnh sửa nếu cần)
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
                'details.toppings.food_toppings.toppings',
                'tables'
            ])->where('user_id', $value)->orderBy('id', 'desc')->first();
        } else {
            $reservation = Order::with([
                'details.foods',
                'details.toppings.food_toppings.toppings',
                'tables'
            ])->find($value);
        }

        if (!$reservation) {
            return response()->json([
                'status' => false,
                'mess' => 'Không tìm thấy đơn đặt bàn.',
            ], 404);
        }

        $details = $reservation->details->map(function ($detail) {

            return [
                'id' => $detail->id,
                'food_id' => $detail->food_id,
                'food_name' => $detail->foods->name ?? null,
                'quantity' => $detail->quantity,
                'price' => $detail->price,
                'image' => $detail->foods->image ?? null,
                'type' => $detail->type,

                'toppings' => $detail->toppings->map(function ($toppings) {
                    return [
                        'food_toppings_id' => $toppings->food_toppings_id,
                        'topping_name' => $toppings->food_toppings->toppings->name ?? null,
                        'price' => $toppings->price
                    ];
                })
            ];
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

        return response()->json([
            'status' => true,
            'mess' => 'Lấy thông tin thành công',
            'info' => [
                'id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'discount_id' => $reservation->discount_id,
                'order_time' => $reservation->order_time,
                'order_status' => $reservation->order_status,
                'total_price' => $reservation->total_price,
                'ex_price' => $reservation->total_price + $reservation->money_reduce,
                'comment' => $reservation->comment,
                'review_time' => $reservation->review_time,
                'rating' => $reservation->rating,
                'guest_name' => $reservation->guest_name,
                'guest_phone' => $reservation->guest_phone,
                'guest_email' => $reservation->guest_email,
                'guest_address' => $reservation->guest_address,
                'guest_count' => $reservation->guest_count,
                'note' => $reservation->note,
                'deposit_amount' => $reservation->deposit_amount,
                'money_reduce' => $reservation->money_reduce,
                'check_in_time' => $reservation->check_in_time,
                'reservations_time' => $reservation->reservations_time,
                'expiration_time' => $reservation->expiration_time,
                'details' => $details,
                'tables' => $tables,

            ]
        ], 200);
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

    //huỷ đơn
    public function cancelOrder(Request $request)
    {
        $order = Order::find($request->id);
        $reserves = Reservation_table::where('order_id', $request->id)->get();

        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng.'], 404);
        }

        $order->order_status = 'Đã hủy';
        $order->save();

        foreach ($reserves as $reserve) {
            $reserve->reservation_status = 'Đã hủy';
            $reserve->save();
        }

        return response()->json(['message' => 'Đơn hàng đã được hủy thành công.']);
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

    //lấy tất cả bàn
    public function getTables()
    {
        $tables = Table::orderBy('capacity', 'asc')->get();
        return $tables;
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
                    'deposit_amount' => $order->deposit_amount,
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
                    'deposit_amount' => $order->deposit_amount,
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

            // Sắp xếp dữ liệu
            $sortedData = $data->sort(function ($a, $b) {
                $statusA = $a['order_status'];
                $statusB = $b['order_status'];

                // Chuyển đổi thời gian thành đối tượng Carbon để so sánh dễ dàng
                $timeA = ($statusA === 'Khách đã đến' && $a['check_in_time'])
                    ? Carbon::parse($a['check_in_time'])
                    : Carbon::parse($a['order_time']);

                $timeB = ($statusB === 'Khách đã đến' && $b['check_in_time'])
                    ? Carbon::parse($b['check_in_time'])
                    : Carbon::parse($b['order_time']);

                // Ưu tiên 'Khách đã đến' và so sánh theo check_in_time
                if ($statusA === 'Khách đã đến' && $statusB !== 'Khách đã đến') {
                    return -1; // A ưu tiên hơn B
                } elseif ($statusA !== 'Khách đã đến' && $statusB === 'Khách đã đến') {
                    return 1; // B ưu tiên hơn A
                } else {
                    // Cả hai cùng trạng thái 'Khách đã đến' hoặc cả hai không phải 'Khách đã đến'
                    return $timeA->timestamp - $timeB->timestamp; // Sắp xếp theo thời gian tăng dần
                }
            })->values(); // Đảm bảo các khóa mảng được đặt lại sau khi sắp xếp

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
                'reserved_from' => 'required|date',
                'reserved_to' => 'nullable|date',
            ], [
                'table_ids.required' => 'Bạn chưa xếp bàn cho đơn hàng này.',
            ]);

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

                Table::where('id', $table_id)->update([
                    'status' => 'Đã đặt trước'
                ]);
            }


            Order::where('id', $data['order_id'])->update([
                'reservation_status' => 'Đã xếp bàn'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Xếp bàn thành công và trạng thái bàn đã thay đổi thành "Đã đặt trước".',
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

        $order->order_status = $request->order_status;
        if ($request->order_status === 'Khách đã đến') {
            $order->check_in_time = Carbon::now();
        }
        if (in_array($request->order_status, ['Đã hủy', 'Hoàn thành'])) {
            foreach ($order->tables as $table) {
                $table->status = 'Bàn trống';
                $table->save();
            }
        }
        $order->save();
        return response()->json([
            'status' => $request->order_status,
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
}
