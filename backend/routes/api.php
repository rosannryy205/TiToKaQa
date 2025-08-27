<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminCategoryToppingController;
use App\Http\Controllers\AdminFoodController;
use App\Http\Controllers\AdminFoodPost;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\AdminToppingController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatRealTimeController;
use App\Http\Controllers\ClaimPrizesController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealFoodsController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Socialite\ProviderCallbackController;
use App\Http\Controllers\Socialite\ProviderRedirectController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\LuckyWheelController;
use App\Http\Controllers\MessengerWebhookController;
use App\Models\LuckyWheelPrize;
use Google\Cloud\Dialogflow\V2\MessageEntry\Role;
use Illuminate\Http\Request;

Route::post('/chatbot', [ChatbotController::class, 'chat']);

// home food
Route::get('/home/foods', [FoodController::class, 'getAllFoods']);
Route::get('/home', [HomeController::class, 'index']);
Route::get('/flash-sale/foods', [FoodController::class, 'getFlashSaleFoods']);
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
Route::get('/home/categories', [CategoryController::class, 'getParentCategories']);
Route::get('/home/all-categories', [CategoryController::class, 'getAllCategories']);
Route::get('/home/category/{id}', [CategoryController::class, 'getCategoryById']);


//reservation-client
Route::post('/reservation', [OrderController::class, 'reservation']);
Route::post('/make-reservation-quickly', [OrderController::class, 'makeReservationQuickly']);
Route::post('/reservation-by-chatbot', [ChatbotController::class, 'combine']);
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
Route::get('/admin/payments/vnpay-return', [PaymentController::class, 'vnpayReturnAdmin']);
Route::post('/admin/payments/cod-payment', [PaymentController::class, 'handleCodPayAdmin']);



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



//permission
Route::get('/permission', [PermissionController::class, 'getAllPermission']);
Route::delete('/permission/{id}', [PermissionController::class, 'deletePermission']);



// message
Route::post('/messages/send', [ChatBotController::class, 'handleMessage']);




Route::get('/invoice/{id}', [OrderController::class, 'generateInvoice']);

//history
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/order-history-info', [OrderController::class, 'getInfoOrderByUser']);
});
Route::put('/order-history-info/cancel/{id}', [OrderController::class, 'cancelOrder']);
Route::put('/order-history-info/update-address/{id}', [OrderController::class, 'updateAddressForOrder']);
//huy cho guess
Route::prefix('orders')->controller(OrderController::class)->group(function () {
    Route::get('lookup', 'lookup')->name('orders.lookup')->middleware('throttle:30,1');
    Route::get('{code}', 'show')->name('orders.show')->where('code', '[A-Za-z0-9\-._]+')->middleware('throttle:60,1');
});
Route::post('orders/{code}/cancel', [OrderController::class, 'cancelByConfirm'])
    ->where('code', '[A-Za-z0-9\-\._]+')
    ->middleware('throttle:15,1');

// Route::resource('user', UserController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::patch('/user/updateProfile/{id}', [UserController::class, 'update']);
    Route::post('/user/{id}/upload-avatar', [UserController::class, 'uploadAvatar']);
});

Route::post('/insert_staff', [UserController::class, 'insertStaff']);


// đăng ký đăng nhập quên mật khẩu
Route::post('/register/send-code', [UserController::class, 'sendRegisterCode']);
Route::post('/register/verify-code', [UserController::class, 'verifyRegisterCode']);

Route::post('/login', [UserController::class, 'login'])->name('login');;
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
Route::post('/reorder/{id}', [CartController::class, 'reOrder']);
Route::post('/ordertakecaway', [CartController::class, 'orderTakeAway']);
Route::put('/update/order/{id}', [OrderController::class, 'reservationUpdate']);
Route::put('/update/reservation-order/{id}', [OrderController::class, 'reservationUpdatePrice']);
Route::post('/order/{id}/complete', [OrderController::class, 'handelOrderComplete']);
Route::post('/order/{id}/cancel', [OrderController::class, 'handelOrderCancel']);


//admin_order
Route::get('/order_detail/{id}', [CartController::class, 'get_order_detail']);
Route::get('/get_all_orders', [CartController::class, 'get_all_orders']);
Route::put('/update/{id}/status', [CartController::class, 'update_status']);
Route::middleware('auth:sanctum')->get('/shipper/orders', [OrderController::class, 'getOrdersByShipper']);
Route::post('/selected_orders', [OrderController::class, 'assignShipper']);
Route::get('/shipper/{id}/active-orders', [OrderController::class, 'getShipperOrders']);
Route::post('/shipper/update-location', [UserController::class, 'updateLocation']);
Route::get('/shipper/{id}/last-location', [UserController::class, 'getLastLocation']);




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
Route::put('/admin/food/{id}/status', [AdminFoodController::class, 'updateStatus']);
Route::get('/admin/food/{id}', [AdminFoodController::class, 'getFoodById']);
Route::post('/admin/update-food/{id}', [AdminFoodController::class, 'update']);
Route::get('/admin/topping-food', [AdminFoodController::class, 'getAlltopping']);

Route::get('/admin/food/topping/{food}', [AdminFoodController::class, 'getToppingForFood']);
Route::post('/admin/food/topping/{food}', [AdminFoodController::class, 'storeToppingForFood']);


// topping
Route::resource('/admin/toppings', AdminToppingController::class);
Route::get('/admin/toppingById/{id}', [AdminToppingController::class, 'getToppingById']);


// danh mục món ăn
Route::get('/admin/categories', [AdminCategoryController::class, 'getParentCategories']);
Route::get('/admin/categories', [AdminCategoryController::class, 'getAllCategories']);
Route::get('/admin/categories/parents/list', [AdminCategoryController::class, 'getParents']);
Route::get('/admin/categories/list', [AdminCategoryController::class, 'index']);
Route::get('/admin/categories/{id}', [AdminCategoryController::class, 'show']);
Route::post('/admin/categories', [AdminCategoryController::class, 'store']);
Route::put('/admin/categories/{id}', [AdminCategoryController::class, 'update']);
Route::delete('/admin/categories/{id}', [AdminCategoryController::class, 'destroy']);
Route::post('/admin/categories/delete-multiple', [AdminCategoryController::class, 'deleteMultiple']);

//  ------- Thống kê admin --------
Route::get('/admin/revenue-by-month', [DashboardController::class, 'revenueByMonth']);
Route::get('/admin/get-dashboard-stats', [DashboardController::class, 'getDashboardStats']);
// người dùng
Route::get('/admin/get-total-users', [DashboardController::class, 'getTotalUser']);
Route::get('/admin/stats-user-by-time', [DashboardController::class, 'statsUserByTime']);
// Đặt bàn
Route::get('/admin/get-total-res', [DashboardController::class, 'getTotalRes']);
Route::get('/admin/stats-res-by-time', [DashboardController::class, 'statsResByTime']);
// Đơn hàng
Route::get('/admin/get-total-order', [DashboardController::class, 'getTotalOrder']);
Route::get('/admin/stats-order-by-time', [DashboardController::class, 'statsOrderByTime']);
// --------------------------------




//adminfood
Route::get('/admin/foods', [AdminFoodController::class, 'getAllFood']);

//paymentMethod
Route::get('/payments/info/{id}', [PaymentController::class, 'show']);
Route::post('/payments/vnpay-init', [PaymentController::class, 'store']);
Route::get('/payments/vnpay-return', [PaymentController::class, 'vnpayReturn']);
Route::post('/payments/cod-payment', [PaymentController::class, 'handleCodPayment']);
Route::get('/get-order-reservation-info', [OrderController::class, 'getOrderReservationInfo']);
Route::resource('test', PaymentController::class);

/**client vong quay*/
Route::get('/lucky-wheel/prizes', [LuckyWheelController::class, 'getPrizes']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/spin', [LuckyWheelController::class, 'spin']);
    Route::get('/user-rewards', [LuckyWheelController::class, 'getUserRewards']);
    Route::post('/claim-reward', [ClaimPrizesController::class, 'claimPrize']);
    Route::get('/spin-status', [LuckyWheelController::class, 'checkSpinStatus']);
    Route::get('/deals-food', [DealFoodsController::class, 'getDealsFood']);
});

/**client user-point_exchange*/
Route::post('/redeem-discount', [DiscountController::class, 'redeem'])->middleware('auth:sanctum');
Route::get('/user-vouchers', [DiscountController::class, 'getUserDiscounts'])->middleware('auth:sanctum');
/** crud combo mqua*/
Route::get('/admin/foods', [FoodController::class, 'getAllFoods']);
// Route::get('/admin/categories', [CategoryController::class, 'getAllCategories']);
Route::get('/admin/combos', [ComboController::class, 'getAllCombosForAdmin']);
Route::get('/admin/combos/{id}', [ComboController::class, 'getComboById']);
Route::post('/admin/combos/create', [ComboController::class, 'createCombosByAdmin']);
Route::post('/admin/combos/update/{id}', [ComboController::class, 'updateCombosForAdmin']);
Route::delete('/admin/combos/delete/{id}', [ComboController::class, 'deleteCombosForAdmin']);



// nguyên liệu
Route::apiResource('ingredients', IngredientController::class);
Route::put('/admin/combos/{id}/toggle-status', [ComboController::class, 'toggleStatusComboForAdmin']);

/** Admin / FOOD_POST */
Route::get('/get_all_post', [AdminFoodPost::class, 'index']);
Route::get('/get_all_food', [AdminFoodPost::class, 'getAllFoods']);
Route::get('/get_post/{id}', [AdminFoodPost::class, 'getPostById']);
Route::post('/post/{id}/update', [AdminFoodPost::class, 'updatePost']);
Route::post('/insert_post', [AdminFoodPost::class, 'store']);
Route::post('/post/{id}/toggle-hide', [AdminFoodPost::class, 'hidePost']);


/** Generate Post */
Route::post('/generate/post', [AIController::class, 'generatePost']);
Route::post('/check-seo', [AIController::class, 'checkSeo']);
/** crud discounts mqua*/
Route::get('/admin-categories', [CategoryController::class, 'getAllCategoriesForAdmin']);
Route::post('/admin/discounts/create', [DiscountController::class, 'createDiscounts']);
Route::get('/admin/discounts/{id}', [DiscountController::class, 'getDiscountById']);
Route::put('/admin/discounts/update/{id}', [DiscountController::class, 'updateDiscountByAdmin']);
Route::patch('/admin/discounts/{id}/status', [DiscountController::class, 'setStatusByAdmin']);
/**prize */
Route::post('/admin/luckyprize/create', [LuckyWheelController::class, 'createLuckyPrizeByAdmin']);
Route::get('/admin/luckyprizes/{id}', [LuckyWheelController::class, 'getLuckyPrizeById']);
Route::put('/admin/luckyprize/update/{id}', [LuckyWheelController::class, 'updateLuckyPrizeByAdmin']);
Route::patch('/admin/luckyprize/{id}/status', [LuckyWheelController::class, 'setStatusPrizeByAdmin']);

