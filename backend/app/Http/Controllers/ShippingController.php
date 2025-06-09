<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    public function getGHNServices(Request $request)
    {
        $params = [
            'shop_id' => (int) env('GHN_SHOP_ID'),
            'from_district' => (int) env('GHN_FROM_DISTRICT'),
            'to_district' => (int) $request->to_district_id,
        ];
    
        Log::info('📦 GHN Service Params:', $params);
    
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Token' => env('GHN_TOKEN'),
            'ShopId' => env('GHN_SHOP_ID'),
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', $params);
        Log::info('📦 GHN Service Response:', $response->json());
    
        return response()->json($response->json()['data'] ?? []);
    }
}  
    

