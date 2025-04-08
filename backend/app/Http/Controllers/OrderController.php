<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //đặt bàn
    public function reservation(Request $request){
        // logger()->info('Request received:', $request->all());
        // return response()->json(['data' => $request->all()]);
        $data = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|digits:10',
            'guest_email' => 'required|email',
            'guest_count' => 'required',
            'reservations_time' => 'required|date',
            'note' => 'required|string',
            'deposit_amount' => 'numeric|min:0',
            'expiration_time' => 'required|date'
        ]);
        try {
            $reservation = Order::create($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        if($reservation){
            return response()->json(['status' => true, 'message' => 'Đặt bàn thành công'], 200);
        }else{
            return response()->json(['status' => false, 'message' => 'Đặt bàn không thành công']);
        }
    }
}
