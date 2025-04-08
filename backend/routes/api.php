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
<<<<<<< HEAD
use App\Http\Controllers\UserController;

Route::post('/chatbot', [ChatbotController::class, 'chat']);
Route::get('/home', [HomeController::class, 'index']);


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');


=======
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ToppingController;
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
<<<<<<< HEAD
Route::get('/home/category/{id}', [CategoryController::class, 'getCategoryById']);
//cart
Route::post('cart/food/{id}', [CartController::class, 'addToCart']);
=======

//order reservation
Route::post('/reservation', [OrderController::class, 'reservation']);
>>>>>>> 30968df47b6250c6eb1eb51e86f7103d8e5b3f76
>>>>>>> 2b5e8a8919db2e94b9d162d4a2edece4539ef45d
