<?php
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

Route::post('/chatbot', [ChatbotController::class, 'chat']);
Route::get('/home', [HomeController::class, 'index']);

use App\Http\Controllers\OrderController;

Route::post('/chatbot', [ChatbotController::class, 'chat']);
// home food
Route::get('/home/foods', [FoodController::class, 'getAllFoods']);
Route::get('/home/food/{id}', [FoodController::class, 'getFoodById']);
Route::get('/home/category/{id}/food', [FoodController::class, 'getFoodByCategory']);
//home combo
Route::get('/home/combos', [ComboController::class, 'getAllCombos']);
Route::get('/home/combo/{id}', [ComboController::class, 'getComboById']);
// home toppings
Route::get('/home/topping/{id}', [FoodController::class, 'getToppingByFood']);
// home categories
Route::get('/home/categories', [CategoryController::class, 'getAllCategories']);
Route::get('/home/category/{id}', [CategoryController::class, 'getCategoryById']);


//reservation
Route::post('/reservation', [OrderController::class, 'reservation']);
Route::get('/order-reservation-info', [OrderController::class, 'getInfoReservation']);

//history
Route::get('/order-history-info/{id}', [OrderController::class, 'getInfoOrderByUser']);
Route::put('/order-history-info/cancle/{id}', [OrderController::class, 'cancelOrder']);
Route::put('/order-history-info/update-address/{id}', [OrderController::class, 'updateAddressForOrder']);

// Route::resource('user', UserController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('user', UserController::class);
});


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/forgot',[UserController::class,'forgotPass']);
Route::post('/code',[UserController::class,'verifyResetCode']);
Route::post('/reset-password',[UserController::class,'ChangePassword']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});



