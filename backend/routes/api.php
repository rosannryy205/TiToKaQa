<?php
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

Route::post('/chatbot', [ChatbotController::class, 'chat']);
Route::get('/home', [HomeController::class, 'index']);

use App\Http\Controllers\OrderController;
use App\Http\Controllers\Socialite\ProviderCallbackController;
use App\Http\Controllers\Socialite\ProviderRedirectController;
use App\Models\Discount;
use Laravel\Socialite\Contracts\Provider;

Route::post('/chatbot', [ChatbotController::class, 'chat']);
// home food
Route::get('/home/foods', [FoodController::class, 'getAllFoods']);
//search
// Route::get('/foods/search', [FoodController::class, 'search'])   ;

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
//reservation - tables - admin
Route::get('/tables', [OrderController::class, 'getTables']);
Route::get('/order-tables', [OrderController::class, 'getOrderOfTable']);
Route::post('/set-up/order-tables', [OrderController::class, 'setUpTable']);
Route::post('/available-tables', [OrderController::class, 'getAvailableTables']);
Route::get('/foods', [OrderController::class, 'getAllFoodsWithToppings']);
Route::post('/reservation-update-status', [OrderController::class, 'updateStatus']);
Route::get('/auto-cancel-orders', [OrderController::class, 'autoCancelOrders']);
Route::get('/unavailable-times', [OrderController::class, 'getUnavailableTimes']);
Route::post('/order-for-user', [OrderController::class, 'orderFoodForUser']);


//history
Route::get('/order-history-info/{id}', [OrderController::class, 'getInfoOrderByUser']);
Route::put('/order-history-info/cancle/{id}', [OrderController::class, 'cancelOrder']);
Route::put('/order-history-info/update-address/{id}', [OrderController::class, 'updateAddressForOrder']);

// Route::resource('user', UserController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('user', UserController::class);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// đăng ký đăng nhập quên mật khẩu
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/forgot',[UserController::class,'forgotPass']);
Route::post('/code',[UserController::class,'verifyResetCode']);
Route::post('/reset-password',[UserController::class,'ChangePassword']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});

// tìm kiếm

// 1. Route để hiển thị kết quả ban đầu
Route::get('/search', [HomeController::class, 'search']);





//cart
Route::post('/order',[CartController::class,'order']);

Route::put('/update/order/{id}',[OrderController::class,'reservationUpdate']);
Route::put('/update/reservation-order/{id}',[OrderController::class,'reservationUpdatePrice']);

Route::get('/order_detail/{id}',[CartController::class,'get_order_detail']);
Route::get('/get_all_orders',[CartController::class,'get_all_orders']);


//discount
Route::get('/discounts',[DiscountController::class,'getAllDiscounts']);


//gg
Route::get('/auth/{provider}/redirect', ProviderRedirectController::class)->name('auth.redirect');
Route::get('/auth/{provider}/callback', ProviderCallbackController::class)->name('auth.callback');


