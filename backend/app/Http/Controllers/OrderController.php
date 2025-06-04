<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_detail;
use App\Models\Order_topping;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Food;
use App\Models\Food_topping;
use App\Models\Reservation_table;
use App\Models\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

Carbon::setLocale('vi');
date_default_timezone_set('Asia/Ho_Chi_Minh');

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    //tìm bàn
    public function getAvailableTables(Request $request)
    {
        $from = $request->input('reserved_from');
        $to = $request->input('reserved_to');
        $numberOfGuests = $request->input('number_of_guests');

        $conflictingTableIds = Reservation_table::where(function ($query) use ($from, $to) {
            $query->where(function ($q) use ($from, $to) {
                $q->where('reserved_from', '<', $to)
                    ->where('reserved_to', '>', $from)
                    ->whereNotIn('reservation_status', ['Đã Hủy', 'Hoàn Thành']);
            });
        })->pluck('table_id')->toArray();

        // lấy tất cả bàn phù hợp có capacity >= số khách và không bị trùng
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

        // lấy sức chứa nhỏ nhất phù hợp
        $minCapacity = $availableTables->first()->capacity;

        // chỉ lấy các bàn có cùng capacity nhỏ nhất
        $priorityTables = $availableTables->filter(function ($table) use ($minCapacity) {
            return $table->capacity == $minCapacity;
        })->values();

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
            $data = $request->validate([
                'user_id' => 'nullable|numeric',
                'guest_name' => 'required|max:255',
                'guest_phone' => 'required|digits:10',
                'guest_email' => 'required|email',
                'note' => 'nullable|string',
                'deposit_amount' => 'nullable|numeric|min:0',
                'total_price' => 'required|numeric',
                'order_detail' => 'nullable|array',
                'discount_id' => 'nullable|numeric',
                'money_reduce' => 'nullable|numeric|min:0',
            ], [
                'guest_name.required' => 'Vui lòng nhập họ tên.',
                'guest_email.required' => 'Vui lòng nhập email.',
                'guest_email.email' => 'Email không đúng định dạng.',
                'guest_phone.required' => 'Vui lòng nhập số điện thoại.',
                'guest_phone.digits' => 'Số điện thoại không đúng định dạng.',
            ]);

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
                'discount_id' => $data['discount_id'] ?? null,
                'guest_name' => $data['guest_name'],
                'guest_phone' => $data['guest_phone'],
                'guest_email' => $data['guest_email'],
                'note' => $data['note'] ?? null,
                'deposit_amount' => $data['deposit_amount'] ?? null,
                'total_price' => $data['total_price'],
                'money_reduce' => $data['money_reduce'] ?? null
            ]);

            if (!empty($data['order_detail'])) {
                foreach ($data['order_detail'] as $item) {
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

            $orderDetailsWithNames = [];
            if (!empty($data['order_detail'])) {
                foreach ($data['order_detail'] as $item) {
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

            $mailData = [
                'order_id' => $order->id,
                'guest_name' => $data['guest_name'],
                'guest_email' => $data['guest_email'],
                'guest_phone' => $data['guest_phone'],
                'total_price' => $data['total_price'],
                'note' => $data['note'] ?? null,
                'order_detail' => $orderDetailsWithNames,
            ];

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


    public function orderFoodForUser(Request $request)
    {
        try {
            $data = $request->validate([
                'price' => 'required|numeric',
                'order_id' => 'required|numeric',
                'food_id' => 'required|numeric',
                'combo_id' => 'nullable|numeric',
                'quantity' => 'required|numeric',
                'type' => 'required|string',
                'order_toppings' => 'nullable|array',
                'order_toppings.*.food_toppings_id' => 'required|numeric',
                'order_toppings.*.price' => 'required|numeric',
            ]);

            $orderDetail = Order_detail::create([
                'order_id' => $data['order_id'],
                'food_id' => $data['food_id'],
                'combo_id' => $data['combo_id'] ?? null,
                'quantity' => $data['quantity'],
                'price' => $data['price'],
                'type' => $data['type'],
            ]);

            // Thêm topping nếu có
            if (!empty($data['order_toppings'])) {
                foreach ($data['order_toppings'] as $toppingId) {
                    $foodTopping = Food_topping::find($toppingId);
                    if ($foodTopping) {
                        Order_topping::create([
                            'food_toppings_id' => $toppingId['food_toppings_id'],
                            'order_detail_id' => $orderDetail->id,
                            'price' => $toppingId['price'], // hoặc $foodTopping->price nếu bạn luôn lấy từ DB
                        ]);
                    }
                }
            }

            return response()->json(['status' => true, 'message' => 'Thêm món thành công!']);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }


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


    public function getInfoOrderByUser(Request $request)
    {
        $userId = $request->id;

        $orders = Order::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

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

    public function getTables()
    {
        $tables = Table::orderBy('capacity', 'asc')->get();
        return $tables;
    }

    public function getOrderOfTable()
    {
        $orders = Order::with(['tables'])->get();


        $orderWithTables = $orders->map(function ($order) {
            return [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'order_status' => $order->order_status,
                'total_price' => $order->total_price,
                'guest_name' => $order->guest_name,
                'guest_phone' => $order->guest_phone,
                'guest_email' => $order->guest_email,
                'guest_address' => $order->guest_address,
                'guest_count' => $order->guest_count,
                'comment' => $order->comment,
                'reservations_time' => $order->reservations_time,
                'check_in_time' => $order->check_in_time,
                'reservation_status' => $order->tables->first()->pivot->reservation_status,
                'table_numbers' => $order->tables->pluck('table_number')->toArray(),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $orderWithTables,
        ]);
    }


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
    public function updateStatus(Request $request)
    {
        $reservation = Reservation_table::find($request->id);
        $order = Order::find($request->id);

        if (!$reservation) {
            return response()->json(['message' => 'Không tìm thấy đơn đặt bàn.'], 404);
        }
        $reservation->reservation_status = $request->reservation_status;
        if ($request->reservation_status === 'Khách Đã Đến') {
            $order->check_in_time = Carbon::now();
        }
        if (in_array($request->reservation_status, ['Đã Hủy', 'Hoàn Thành'])) {
            foreach ($order->tables as $table) {
                $table->status = 'Bàn trống';
                $table->save();
            }
        }
        $reservation->save();
        return response()->json(['message' => 'Đơn hàng đã được cập nhật thành công.']);
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



    public function getOrderByUser($user_id, $id)
    {
        $order = DB::table('orders')
            ->where('user_id', $user_id)
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
