<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function api() {
        return response()->json('hello');
    }
    public function index(){
        $foods= Food::all();
        return response()->json($foods);
    }

}
