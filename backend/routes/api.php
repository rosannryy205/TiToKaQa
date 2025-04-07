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
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

Route::post('/chatbot', [ChatbotController::class, 'chat']);
Route::get('/home', [HomeController::class, 'index']);


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');


