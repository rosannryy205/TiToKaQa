<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class CartController extends Controller
{

    public function check_out(Request $request)
    {
        try{
            $data = $request->validate([
                'user_id' => 'nullable|numeric',
                'guest_name' => 'required|string|max:255',
                'guest_phone' => 'required|digits:10',
                'guest_email' => 'required|email',
                'guest_address' => 'required|address',
                'total_price' => 'required|numeric',
                'order_detail' => 'nullable|array',
                'note' => 'nullable|string',
            ]);

            try {
                $order = Order::create([
                    'user_id' => $data['user_id'] ?? null,
                    'guest_name' => $data['guest_name'],
                    'guest_phone' => $data['guest_phone'],
                    'guest_email' => $data['guest_email'],
                    'guest_address' => $data['guest_address'],
                    'total_price' => $data['total_price'],
                    'note' => $data['note']??null,
                ]);


            if(!empty($data['order_detail'])){
                
            }


            }catch(\Exception $e){

            }
        } catch(\Exception $e){

        }
    }

}
