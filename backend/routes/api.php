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
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ToppingController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Food;
use App\Models\Topping;

Route::post('/chatbot', [ChatbotController::class, 'chat']);
// home food
Route::get('/home/foods', [FoodController::class, 'getAllFoods']);
Route::get('/home/food/{id}', [FoodController::class, 'getFoodById']);
Route::get('/home/category/{id}/food', [FoodController::class, 'getFoodByCategory']);

// home toppings
Route::get('/home/topping/{id}', [FoodController::class, 'getToppingByFood']);
// home categories
Route::get('/home/categories', [CategoryController::class, 'getAllCategories']);
Route::get('/home/category/{id}', [CategoryController::class, 'getCategoryById']);

//reservation
Route::post('/reservation', [OrderController::class, 'reservation']);
Route::get('/reservation-info/{id}', [OrderController::class, 'getInfoReservation']);

//login/register/logout

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
//user
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');


