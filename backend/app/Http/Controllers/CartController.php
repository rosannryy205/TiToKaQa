<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_detail;
use App\Models\Order_topping;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{

    public function order(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'nullable|numeric',
                'guest_name' => 'required|string|max:255',
                'guest_phone' => 'required|digits:10',
                'guest_email' => 'required|email',
                'guest_address' => 'required|string|max:255',
                'total_price' => 'required|numeric',
                'order_detail' => 'nullable|array',
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
            ]);

            try {
                $order = Order::create([
                    'user_id' => $data['user_id'] ?? null,
                    'guest_name' => $data['guest_name'],
                    'guest_phone' => $data['guest_phone'],
                    'guest_email' => $data['guest_email'],
                    'guest_address' => $data['guest_address'],
                    'total_price' => $data['total_price'],
                    'note' => $data['note'] ?? null
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

                        if(!empty($item['toppings'])){
                            foreach($item['toppings'] as $topping){
                                Order_topping::create([
                                    'food_toppings_id' => $topping['food_toppings_id'],
                                    'order_detail_id' => $orderDetail -> id,
                                    'price' => $topping['price'],
                                ]);
                            }
                        }
                    }
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Đặt hàng thành công',
                    'order_id'=> $order->id
                ],200);
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'error' => $e -> getMessage()],500);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ],422);
        }
    }

    public function get_order_detail(Request $request){
        $order = Order::with([
            'details.foods', //lấy tên món ăn
            'details.toppings.food_toppings.toppings' //lấy tên toppingtopping
        ])->find($request -> id);
        if ($order){
            $details = $order->details->map(function ($detail){
                return [
                    'id' => $detail->id,
                    'food_id' => $detail->food_id,
                    'food_name' =>optional($detail->foods)->name,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                    'image' => $detail->foods->image,
                    'type' => $detail->type,
                    'toppings' => $detail->toppings->map(function ($toppings){
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
                    'id' => $order -> id,
                    'user_id' => $order -> user_id,
                    'discount_id' => $order -> discount_id,
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
            ],200);
        } else {
            return response()->json([
                'status' => false,
                'mess' => 'Lấy thông tin không thành công',
            ]);
        }
    }


    public function get_all_orders(){
        $orders = Order::with([
            'details.foods', // lấy tên món ăn
            'details.toppings.food_toppings.toppings' // lấy tên topping
        ])->orderByDesc('order_time')->get();

        if ($orders->isNotEmpty()) {
            $data = $orders->map(function ($order) {
                $details = $order->details->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'food_id' => $detail->food_id,
                        'food_name' => $detail->food_name ?? null,
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'image' => $detail->foods->image,
                        'type' => $detail->type,
                        'toppings' => $detail->toppings->map(function ($toppings) {
                            return [
                                'food_toppings_id' => $toppings->food_toppings_id,
                                'topping_name' => $toppings->food_toppings->topping->name ?? null,
                                'price' => $toppings->price,
                            ];
                        })
                    ];
                });

                // lấy thông tin các bàn của đơn hàng
                $tables = $order->tables->map(function ($table) {
                    return [
                        'table_id' => $table->id,
                        'table_number' => $table->table_number,
                        'assigned_time' => $table->pivot->assigned_time,
                        'reserved_from' => $table->pivot->reserved_from,
                        'reserved_to' => $table->pivot->reserved_to,
                    ];
                });

                return [
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
                    'details' => $details,
                    'tables' => $tables
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


}



