<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/api', function () {
//     return response()->json('hello')
//         ->header('Access-Control-Allow-Origin', '*')
//         ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
//         ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin');
// });

use App\Http\Controllers\ChatbotController;

Route::post('/chatbot', [ChatbotController::class, 'chat']);
