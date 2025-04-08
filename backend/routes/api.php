<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/api', function () {
//     return response()->json('hello')
//         ->header('Access-Control-Allow-Origin', '*')
//         ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
//         ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin');
// });

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\HomeController;
use App\Models\Category;
use App\Models\Food;

Route::post('/chatbot', [ChatbotController::class, 'chat']);
Route::get('/home/foods', [FoodController::class, 'getAllFoods']);
Route::get('/home/food/{id}', [FoodController::class, 'getFoodById']);
//categories
Route::get('/home/categories', [CategoryController::class, 'getAllCategories']);
Route::get('/home/category/{id}', [CategoryController::class, 'getCategoryById']);
//cart
Route::post('cart/food/{id}', [CartController::class, 'addToCart']);
