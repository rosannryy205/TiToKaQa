<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class Discount extends Controller
{
    public function getAllDiscounts(){
        $discounts = Discount::all();
        return response()->json($discounts);
    }
}
