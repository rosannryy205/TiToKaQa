<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_detail;
use App\Models\Order_topping;
use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\Info_order;
use App\Mail\ReservationMail;
use App\Models\Food;
use App\Models\Food_topping;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    //đặt bàn
    public function reservation(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'nullable|numeric',
                'guest_name' => 'string|max:255',
                'guest_phone' => 'digits:10',
                'guest_email' => 'email',
                'guest_count' => 'required|integer|min:1',
                'reservations_time' => 'required|date',
                'note' => 'nullable|string',
                'deposit_amount' => 'nullable|numeric|min:0',
                'expiration_time' => 'required|date',
                'total_price' => 'required|numeric',
                'order_details' => 'nullable|array',
            ]);

            $authUser = Auth::user();
            $userId = $authUser->id ?? $data['user_id'] ?? null;

            $order = Order::create([
                'user_id' => $userId,
                'guest_name' => $userId ? null : $data['guest_name'],
                'guest_phone' => $userId ? null : $data['guest_phone'],
                'guest_email' => $userId ? null : $data['guest_email'],
                'guest_count' => $data['guest_count'],
                'reservations_time' => $data['reservations_time'],
                'note' => $data['note'] ?? null,
                'deposit_amount' => $data['deposit_amount'] ?? 0,
                'expiration_time' => $data['expiration_time'],
                'total_price' => $data['total_price'],
            ]);

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


            $orderDetailsWithNames = [];
            if (!empty($data['order_details'])) {
                foreach ($data['order_details'] as $item) {
                    $name = null;

                    if ($item['type'] === 'food' && !empty($item['food_id'])) {
                        $food = Food::find($item['food_id']);
                        $name = $food?->name ?? 'Món ăn không tồn tại';
                    }

                    // lấy topping nếu có
                    $toppingsWithNames = [];
                    if (!empty($item['toppings'])) {
                        foreach ($item['toppings'] as $topping) {
                            $foodToppingModel = Food_topping::find($topping['food_toppings_id']);
                            $toppingModel = $foodToppingModel?->toppings; // lấy từ quan hệ

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
                'guest_count' => $data['guest_count'],
                'reservations_time' => $data['reservations_time'],
                'total_price' => $data['total_price'],
                'note' => $data['note'] ?? null,
                'order_details' => $orderDetailsWithNames,
            ];



            // // Gửi email xác nhận đặt bàn
            // $emailTo = $mailData['guest_email'];
            // Mail::to($emailTo)->send(new ReservationMail($mailData));



            return response()->json([
                'status' => true,
                'message' => 'Đặt bàn thành công',
                'order_id' => $order->id
            ], 200);
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
                'details.toppings.food_toppings.toppings'
            ])->where('user_id', $value)->orderBy('id', 'desc')->first();
        } else {
            $reservation = Order::with([
                'details.foods',
                'details.toppings.food_toppings.toppings'
            ])->find($value);
        }

        if (!$reservation) {
            return response()->json([
                'status' => false,
                'mess' => 'Không tìm thấy đơn đặt bàn.',
            ], 404);
        }

        $userInfo = null;
        if ($reservation->user_id) {
            $user = ModelsUser::find($reservation->user_id);
            if ($user) {
                $userInfo = [
                    'id' => $user->id,
                    'name' => $user->fullname ?? $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                ];
            }
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

        return response()->json([
            'status' => true,
            'mess' => 'Lấy thông tin thành công',
            'user' => $userInfo, // nếu null thì client biết là khách
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

        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng.'], 404);
        }

        $order->order_status = 'Đã hủy';
        $order->reservation_status = 'Đã hủy';
        $order->save();

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
}
