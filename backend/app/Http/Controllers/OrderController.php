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
            'order_details' => 'required|array',
            'order_details.*.food_id' => 'nullable|numeric',
            'order_details.*.combo_id' => 'nullable|numeric',
            'order_details.*.quantity' => 'required|integer|min:1',
            'order_details.*.price' => 'required|numeric|min:0',
            'order_details.*.type' => 'required|string|in:food,combo, topping',
            'order_details.*.toppings' => 'nullable|array',
            'order_details.*.toppings.*.food_toppings_id' => 'required|numeric',
            'order_details.*.toppings.*.price' => 'required|numeric|min:0',
        ]);
        // dd($data); // Xem dữ liệu sau validate


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
            ]);

            //order_details và toppings
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

            return response()->json(['status' => true, 'message' => 'Đặt bàn thành công'], 200);

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
}

?>
