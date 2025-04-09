<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_detail;
use App\Models\Order_topping;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    //đặt bàn
    public function reservation(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'nullable|numeric',
                'guest_name' => 'required|string|max:255',
                'guest_phone' => 'required|digits:10',
                'guest_email' => 'required|email',
                'guest_count' => 'required|integer|min:1',
                'reservations_time' => 'required|date',
                'note' => 'nullable|string',
                'deposit_amount' => 'nullable|numeric|min:0',
                'expiration_time' => 'required|date',
                'total_price' => 'required|numeric',
                'order_details' => 'nullable|array',
            ]);

            try {
                $order = Order::create([
                    'user_id' => $data['user_id'] ?? null,
                    'guest_name' => $data['guest_name'],
                    'guest_phone' => $data['guest_phone'],
                    'guest_email' => $data['guest_email'],
                    'guest_count' => $data['guest_count'],
                    'reservations_time' => $data['reservations_time'],
                    'note' => $data['note'] ?? null,
                    'deposit_amount' => $data['deposit_amount'] ?? 0,
                    'expiration_time' => $data['expiration_time'],
                    'total_price' => $data['total_price'],
                ]);

                //order_details và toppings
                if (!empty($data['order_details'])) {
                    foreach ($data['order_details'] as $item) {
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
                return response()->json([
                    'status' => true,
                    'message' => 'Đặt bàn thành công',
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

    public function getInfoReservation(Request $request)
    {
        $reservation = Order::with([
            'details.foods',  // lấy tên món ăn
            'details.toppings.food_toppings.toppings'  // lấy tên topping
        ])->find($request->id);
        if ($reservation) {
            $details = $reservation->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'food_id' => $detail->food_id,
                    'food_name' => $detail->foods->name ?? null,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                    'image' => $detail->foods->image,
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
                    'check_in_time' => $reservation->check_in_time,
                    'reservations_time' => $reservation->reservations_time,
                    'expiration_time' => $reservation->expiration_time,
                    'reservation_status' => $reservation->reservation_status,
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

}
