<?php

use App\Http\Controllers\AdminCategoryController;
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
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Socialite\ProviderCallbackController;
use App\Http\Controllers\Socialite\ProviderRedirectController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\TableController;
use App\Models\Combo;

Route::post('/chatbot', [ChatbotController::class, 'chat']);

// home food
Route::get('/home/foods', [FoodController::class, 'getAllFoods']);
Route::get('/home', [HomeController::class, 'index']);

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


//reservation-client
Route::post('/reservation', [OrderController::class, 'reservation']);
Route::get('/order-reservation-info', [OrderController::class, 'getInfoReservation']);
Route::post('/choose-table', [OrderController::class, 'chooseTable']);


//reservation-admin
Route::get('/order-tables', [OrderController::class, 'getOrderOfTable']);
Route::get('/order-current-tables', [OrderController::class, 'getCurrentOrder']);
Route::put('/change-table', [OrderController::class, 'changeTable']);
Route::post('/set-up/order-tables', [OrderController::class, 'setUpTable']);
Route::post('/available-tables', [OrderController::class, 'getAvailableTables']);
Route::get('/foods', [OrderController::class, 'getAllFoodsWithToppings']);
Route::post('/reservation-update-status', [OrderController::class, 'updateStatus']);
Route::get('/auto-cancel-orders', [OrderController::class, 'autoCancelOrders']);
Route::get('/unavailable-times', [OrderController::class, 'getUnavailableTimes']);
Route::get('/load-order-detail/{order_id}', [OrderController::class, 'showOrderDetail']);
Route::put('/update-order-detail/{order_id}', [OrderController::class, 'updateOrderDetails']);


// table-admin
Route::get('/tables', [TableController::class, 'getTables']);
Route::get('/all-tables', [TableController::class, 'getAllTable']);
Route::get('/tables/{id}', [TableController::class, 'getTableById']);
Route::put('/tables/{id}', [TableController::class, 'updateTable']);
Route::delete('/tables/{id}', [TableController::class, 'deleteTable']);
Route::get('/get-orders-tables/{id}', [TableController::class, 'getAllOrdersByIdTable']);
Route::post('/insert-table', [TableController::class, 'insertTable']);


// role
Route::get('/role', [RoleController::class, 'getAllRole']);
Route::get('/role-permission/{id}', [RoleController::class, 'getAllPermission']);
Route::put('/role-permission-update', [RoleController::class, 'updatePermission']);
Route::post('/role-permission-create', [RoleController::class, 'createRoleWithPermissions']);
Route::get('/role-permission-user/{id}', [RoleController::class, 'userProfile']);
Route::delete('/delete_role/{id}', [RoleController::class, 'deleteRole']);





Route::get('/invoice/{id}', [OrderController::class, 'generateInvoice']);

//history
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/order-history-info', [OrderController::class, 'getInfoOrderByUser']);
});
Route::put('/order-history-info/cancel/{id}', [OrderController::class, 'cancelOrder']);
Route::put('/order-history-info/update-address/{id}', [OrderController::class, 'updateAddressForOrder']);

// Route::resource('user', UserController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::patch('/user-update/{id}', [UserController::class, 'update']);
    // Route::post('/user/upload-avatar', [UserController::class, 'uploadAvatar']);
});

Route::post('/insert_staff', [UserController::class, 'insertStaff' ]);


// đăng ký đăng nhập quên mật khẩu
Route::post('/register/send-code', [UserController::class, 'sendRegisterCode']);
Route::post('/register/verify-code', [UserController::class, 'verifyRegisterCode']);

Route::post('/login', [UserController::class, 'login']);
Route::post('/forgot', [UserController::class, 'forgotPass']);
Route::post('/verify-code', [UserController::class, 'verifyResetCode']);
Route::post('/reset-password', [UserController::class, 'ChangePassword']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});

// tìm kiếm

// 1. Route để hiển thị kết quả ban đầu
Route::get('/search', [HomeController::class, 'search']);


//delivery
Route::get('/delivery/{id}', [OrderController::class, 'getOrderByUser']);
//tinh phi ship
Route::post('/ghn/service', [ShippingController::class, 'getGHNServices']);





//getall user
Route::resource('user', UserController::class);
Route::put('/update/{id}', [UserController::class, 'updateStatus']);
Route::post('/assign-role/{user_id}', [UserController::class, 'assignSingleRole']);






//cart
Route::post('/order', [CartController::class, 'order']);
Route::post('/ordertakecaway', [CartController::class, 'orderTakeAway']);
Route::put('/update/order/{id}', [OrderController::class, 'reservationUpdate']);
Route::put('/update/reservation-order/{id}', [OrderController::class, 'reservationUpdatePrice']);
Route::post('/order/{id}/complete', [OrderController::class, 'handelOrderComplete']);
Route::post('/order/{id}/cancel', [OrderController::class, 'handelOrderCancel']);


//admin_order
Route::get('/order_detail/{id}', [CartController::class, 'get_order_detail']);
Route::get('/get_all_orders', [CartController::class, 'get_all_orders']);
Route::put('/update/{id}/status', [CartController::class, 'update_status']);


//discount
Route::get('/discounts', [DiscountController::class, 'getAllDiscounts']);
Route::post('/discounts/use', [DiscountController::class, 'used']);

//gg
Route::get('/auth/{provider}/redirect', ProviderRedirectController::class)->name('auth.redirect');
Route::get('/auth/{provider}/callback', ProviderCallbackController::class)->name('auth.callback');


//admin
// Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // món ăn
    Route::get('/admin/manage/foods', [AdminFoodController::class, 'index']);
    Route::post('/admin/foods', [AdminFoodController::class, 'store']);
    Route::get('/admin/foods/search', [FoodController::class, 'search']);
    Route::delete('/admin/food/{id}', [AdminFoodController::class, 'destroy']);
    Route::post('/admin/foods/delete-multiple', [AdminFoodController::class, 'deleteMultiple']);
    Route::put('admin/food/{id}/status', [AdminFoodController::class, 'updateStatus']);
    Route::get('/admin/food/{id}', [AdminFoodController::class, 'getFoodById']);
    Route::post('/admin/update-food/{id}', [AdminFoodController::class, 'update']);


    // topping
    Route::resource('/admin/category_topping', AdminCategoryToppingController::class);
    Route::resource('/admin/toppings', AdminToppingController::class);
    Route::get('/admin/toppingById/{id}', [AdminToppingController::class,'getToppingById']);


    // danh mục món ăn
    Route::get('/admin/categories', [AdminCategoryController::class, 'getAllCategories']);
    Route::get('/admin/categories/parents/list', [AdminCategoryController::class, 'getParents']);
    Route::get('/admin/categories/list', [AdminCategoryController::class, 'index']);
    Route::get('/admin/categories/{id}', [AdminCategoryController::class, 'show']);
    Route::post('/admin/categories', [AdminCategoryController::class, 'store']);
    Route::put('/admin/categories/{id}', [AdminCategoryController::class, 'update']);
    Route::delete('/admin/categories/{id}', [AdminCategoryController::class, 'destroy']);
    Route::post('/admin/categories/delete-multiple', [AdminCategoryController::class, 'deleteMultiple']);




    // Route::get('/admin/toppings', [AdminToppingController::class, 'index']);
    // Route::get('/admin/catetop', [AdminCategoryToppingController::class, 'getAll']);
    // Route::post('/admin/toppings', [AdminToppingController::class, 'store']);
// });



//adminfood
Route::get('/admin/foods', [AdminFoodController::class, 'getAllFood']);

//admin combo
Route::get('/admin/combos', [ComboController::class, 'getAllCombos']);


//paymentMethod
Route::post('/payments/vnpay-init', [PaymentController::class, 'store']);
Route::get('/payments/vnpay-return', [PaymentController::class, 'vnpayReturn']);
Route::post('/payments/cod-payment', [PaymentController::class, 'handleCodPayment']);


/**client user-point_exchange*/
Route::post('/redeem-discount', [DiscountController::class, 'redeem'])->middleware('auth:sanctum');

/** crud combo mqua*/
Route::get('/admin/foods', [FoodController::class, 'getAllFoods']);
// Route::get('/admin/categories', [CategoryController::class, 'getAllCategories']);
Route::get('/admin/combos', [ComboController::class, 'getAllCombos']);
Route::get('/admin/combos/{id}', [ComboController::class, 'getComboById']);
Route::post('/admin/combos/create', [ComboController::class, 'createCombosByAdmin']);
Route::post('/admin/combos/update/{id}', [ComboController::class, 'updateCombosForAdmin']);
Route::delete('/admin/combos/delete/{id}', [ComboController::class, 'deleteCombosForAdmin']);

