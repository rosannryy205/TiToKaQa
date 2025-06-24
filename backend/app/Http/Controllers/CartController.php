<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Order;
use App\Models\Order_detail;
use App\Models\Order_topping;
use App\Models\Reservation_table;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{

    public function order(Request $request)
    {
        try {
            $data = $request->validate(
                [
                    'user_id' => 'nullable|numeric',
                    'guest_name' => 'required|string|max:255',
                    'guest_phone' => 'required|digits:10',
                    'guest_email' => 'required|email',
                    'guest_address' => 'required|string|max:255',
                    'total_price' => 'required|numeric',
                    'money_reduce' => 'required|numeric',
                    'order_detail' => 'nullable|array',
                    'discount_id' => 'nullable|numeric',
                    'note' => 'nullable|string',
                ],
                [
                    'guest_name.required' => 'Vui lòng nhập họ tên.',

                    'guest_email.required' => 'Vui lòng nhập email.',
                    'guest_email.email' => 'Email không đúng định dạng.',

                    'guest_phone.required' => 'Vui lòng nhập số điện thoại.',
                    'guest_phone.regex' => 'Số điện thoại không đúng định dạng.',
                    'guest_phone.digits' => 'Số điện thoại không đúng định dạng.',

                    'guest_address.requied' => 'Vui lòng điền địa chỉ nhận hàng',

                    'reservations_time.required' => 'Vui lòng nhập ngày nhận bàn.',
                ]
            );

            try {
                $order = Order::create([
                    'user_id' => $data['user_id'] ?? null,
                    'guest_name' => $data['guest_name'],
                    'guest_phone' => $data['guest_phone'],
                    'guest_email' => $data['guest_email'],
                    'guest_address' => $data['guest_address'],
                    'total_price' => $data['total_price'],
                    'money_reduce' => $data['money_reduce'],
                    'discount_id' => $data['discount_id'],
                    'note' => $data['note'] ?? null,
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

                        // Trừ stock và cộng quantity_sold nếu là món ăn đơn lẻ
                        if (!empty($item['food_id'])) {
                            $food = Food::find($item['food_id']);
                            if ($food) {
                                $food->stock -= $item['quantity'];
                                $food->quantity_sold += $item['quantity'];
                                $food->save();
                            }
                        }

                        //Topping
                        if (!empty($item['toppings'])) {
                            foreach ($item['toppings'] as $topping) {
                                Order_topping::create([
                                    'food_toppings_id' => $topping['food_toppings_id'],
                                    'order_detail_id' => $orderDetail->id,
                                    'price' => $topping['price'],
                                ]);
                            }
                        }
                    }
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Đặt hàng thành công',
                    'order_id' => $order->id
                ], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        }
    }


    public function orderTakeAway(Request $request)
    {
        try {
            $data = $request->validate(
                [
                    'user_id' => 'nullable|numeric',
                    'guest_name' => 'required|string|max:255',
                    'note' => 'nullable|string',
                    'order_detail' => 'nullable|array',
                    'total_price' => 'nullable|numeric',
                    'money_reduce' => 'nullable|numeric',
                    'discount_id' => 'nullable|numeric',
                ],
                [
                    'guest_name.required' => 'Vui lòng nhập họ tên.',
                ]
            );

            try {
                $order = Order::create([
                    'user_id' => $data['user_id'] ?? null,
                    'guest_name' => $data['guest_name'],
                    'guest_phone' => null,
                    'guest_email' => null,
                    'guest_address' => null,
                    'total_price' => $data['total_price'] ?? 0,
                    'money_reduce' => $data['money_reduce'] ?? 0,
                    'discount_id' => $data['discount_id'] ?? null,
                    'note' => $data['note'] ?? null,
                    'order_status' => "Đã xác nhận",
                    'type_order' => "takeaway",
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

                        if (!empty($item['food_id'])) {
                            $food = Food::find($item['food_id']);
                            if ($food) {
                                $food->stock -= $item['quantity'];
                                $food->quantity_sold += $item['quantity'];
                                $food->save();
                            }
                        }

                        if (!empty($item['toppings'])) {
                            foreach ($item['toppings'] as $topping) {
                                Order_topping::create([
                                    'food_toppings_id' => $topping['food_toppings_id'],
                                    'order_detail_id' => $orderDetail->id,
                                    'price' => $topping['price'],
                                ]);
                            }
                        }
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Đặt hàng thành công',
                    'order_id' => $order->id
                ], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        }
    }


    public function get_order_detail(Request $request)
    {
        $order = Order::with([
            'details.foods', //lấy tên món ăn
            'details.toppings.food_toppings.toppings' //lấy tên toppingtopping
        ])->find($request->id);
        if ($order) {
            $details = $order->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'food_id' => $detail->food_id,
                    'food_name' => optional($detail->foods)->name,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                    'image' => $detail->foods->image,
                    'type' => $detail->type,
                    'toppings' => $detail->toppings->map(function ($toppings) {
                        return [
                            'food_toppings_id' => $toppings->food_toppings_id,
                            'topping_name' => optional($toppings->food_toppings->toppings)->name,
                            'price' => $toppings->price,
                        ];
                    })
                ];
            });
            return response()->json([
                'status' => true,
                'mess' => 'Lấy thông tin thành công',
                'order_detail' => [
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
                    'reservations_time' => $order->reservations_time,
                    'expiration_time' => $order->expiration_time,
                    'reservation_status' => $order->reservation_status,
                    'details' => $details
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'mess' => 'Lấy thông tin không thành công',
            ]);
        }
    }


    public function get_all_orders()
    {
        $orders = Order::with([
            'details.foods', // lấy tên món ăn
            'details.toppings.food_toppings.toppings', // lấy tên topping
            'tables',
            'payment'
        ])->orderByDesc('order_time')->get();

        if ($orders->isNotEmpty()) {
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
                    'deposit_amount' => $order->deposit_amount,
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

    public function update_status(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|string|max:255'
        ]);

        $order = Order::with('details', 'payment')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng'], 404);
        }

        $oldStatus = $order->order_status;
        $newStatus = $request->order_status;

        DB::beginTransaction();
        try {
            if ($newStatus === 'Đã hủy' && in_array($oldStatus, ['Chờ xác nhận', 'Đã xác nhận'])) {
                foreach ($order->details as $detail) {
                    $food = Food::find($detail->food_id);
                    if ($food) {
                        $food->stock += $detail->quantity;
                        $food->quantity_sold -= $detail->quantity;
                        $food->save();
                    }
                }
            }

            $order->order_status = $newStatus;
            $order->save();
            //================================
            // POINT
            //================================
            $pointService = new PointService();
            $pointService->updateUserPointsWhenOrderCompleted($order);
            //================================

            if ($order->payment) {
                $payment = $order->payment;

                if ($newStatus === 'Giao thành công') {
                    $payment->payment_status = 'Đã thanh toán';
                } elseif (in_array($newStatus, ['Giao thất bại', 'Đã hủy'])) {
                    $payment->payment_status = 'Thanh toán thất bại';

                    if(in_array($payment->payment_method, ['VNPAY', 'MOMO'])){
                        $payment->payment_status = 'Đã hoàn tiền';
                    }
                }
                // Nếu là thanh toán online và đơn hàng giao thành công thì luôn đảm bảo đánh dấu là đã thanh toán
                if (
                    $newStatus === 'Giao thành công' &&
                    in_array($payment->payment_method, ['VNPAY', 'MOMO'])
                ) {
                    $payment->payment_status = 'Đã thanh toán';
                }

                $payment->save();
            }



            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "Cập nhật trạng thái thành công"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật trạng thái',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
