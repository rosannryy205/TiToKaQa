<?php

use App\Http\Controllers\AdminCategoryToppingController;
use App\Http\Controllers\AdminFoodController;
use App\Http\Controllers\AdminToppingController;
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
use App\Http\Controllers\PaymentController;

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
Route::post('/choose-table', [OrderController::class, 'chooseTable']);
//reservation - tables - admin
Route::get('/tables', [OrderController::class, 'getTables']);
Route::get('/order-tables', [OrderController::class, 'getOrderOfTable']);
Route::put('/change-table', [OrderController::class, 'changeTable']);
Route::post('/set-up/order-tables', [OrderController::class, 'setUpTable']);
Route::post('/available-tables', [OrderController::class, 'getAvailableTables']);
Route::get('/foods', [OrderController::class, 'getAllFoodsWithToppings']);
Route::post('/reservation-update-status', [OrderController::class, 'updateStatus']);
Route::get('/auto-cancel-orders', [OrderController::class, 'autoCancelOrders']);
Route::get('/unavailable-times', [OrderController::class, 'getUnavailableTimes']);


Route::get('/invoice/{id}', [OrderController::class, 'generateInvoice']);
Route::post('/order-for-user', [OrderController::class, 'orderFoodForUser']);

//history
Route::get('/order-history-info/{id}', [OrderController::class, 'getInfoOrderByUser']);
Route::put('/order-history-info/cancel/{id}', [OrderController::class, 'cancelOrder']);
Route::put('/order-history-info/update-address/{id}', [OrderController::class, 'updateAddressForOrder']);

// Route::resource('user', UserController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('user', UserController::class);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// đăng ký đăng nhập quên mật khẩu
Route::post('/register/send-code', [UserController::class, 'sendRegisterCode']);
Route::post('/register/verify-code', [UserController::class, 'verifyRegisterCode']);

Route::post('/login', [UserController::class, 'login']);
Route::post('/forgot',[UserController::class,'forgotPass']);
Route::post('/verify-code',[UserController::class,'verifyResetCode']);
Route::post('/reset-password',[UserController::class,'ChangePassword']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});

// tìm kiếm

// 1. Route để hiển thị kết quả ban đầu
Route::get('/search', [HomeController::class, 'search']);


//delivery
Route::get('/delivery/{user_id}/{id}', [OrderController::class, 'getOrderByUser']);





//cart
Route::post('/order',[CartController::class,'order']);

Route::put('/update/order/{id}',[OrderController::class,'reservationUpdate']);
Route::put('/update/reservation-order/{id}',[OrderController::class,'reservationUpdatePrice']);


//admin_order
Route::get('/order_detail/{id}',[CartController::class,'get_order_detail']);
Route::get('/get_all_orders',[CartController::class,'get_all_orders']);
Route::put('/update/{id}/status',[CartController::class,'update_status']);


//discount
Route::get('/discounts',[DiscountController::class,'getAllDiscounts']);
Route::post('/discounts/use', [DiscountController::class,'used']);

//gg
Route::get('/auth/{provider}/redirect', ProviderRedirectController::class)->name('auth.redirect');
Route::get('/auth/{provider}/callback', ProviderCallbackController::class)->name('auth.callback');


//admin
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/foods', [AdminFoodController::class, 'index']);
    Route::get('/admin/categories', [CategoryController::class, 'getAllCategories']);
    Route::post('/admin/foods', [AdminFoodController::class, 'store']);
    Route::delete('/admin/food/{id}', [AdminFoodController::class, 'destroy']);
    Route::get('/admin/food/{id}', [AdminFoodController::class, 'getFoodById']);
    Route::post('/admin/update-food/{id}', [AdminFoodController::class, 'update']);
    Route::get('/admin/toppings', [AdminToppingController::class, 'index']);
    Route::get('/admin/catetop',[AdminCategoryToppingController::class,'getAll']);
    Route::post('/admin/toppings',[AdminToppingController::class,'store']);
});

//admin combo
Route::get('/admin/combos', [ComboController::class, 'getAllCombos']);



Route::resource('/payment', PaymentController::class);
// routes/api.php
Route::post('/vnpay-return', [PaymentController::class, 'vnpayReturn']);

