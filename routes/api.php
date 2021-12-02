<?php

use App\Http\Controllers\API\AppSettingsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\FavouriteController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\VendorController;
use App\Http\Controllers\API\VendorTypeController;
use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\DeliveryAddressController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\TrackOrderController;
use App\Http\Controllers\API\PackageOrderController;
use App\Http\Controllers\API\RegularOrderController;
use App\Http\Controllers\API\PackageTypeController;
use App\Http\Controllers\API\ChatNotificationController;
use App\Http\Controllers\API\RatingController;
use App\Http\Controllers\API\EarningController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\OrderPaymentCallbackController;
use App\Http\Controllers\API\OTPController;
use App\Http\Controllers\API\SocialLoginController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\VendorPackageTypePricingController;
use App\Http\Controllers\API\VendorServiceController;
use App\Http\Controllers\API\VehicleTypeController;
use App\Http\Controllers\API\TaxiOrderController;
use App\Http\Controllers\API\PaymentAccountController;
use App\Http\Controllers\API\PayoutController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//CRON Job
Route::get('/cron/job', function (Request $request) {
    //
    $appKey  = env("CRON_JOB_KEY", "");
    $urlAppKey = str_ireplace(" ", "+", $request->key);
    //
    if ($appKey != $urlAppKey) {
        return response()->json([
            "message" => "Unauthorized",
        ], 401);
    }

    $artisan = \Artisan::call("schedule:run");
    $output = \Artisan::output();
    return response()->json([
        "message" => "schedule runed",
        "output" => $output
    ]);
})->name('cron.job');

//App settings
Route::get('/app/settings', [AppSettingsController::class, 'index']);


// Auth
Route::post('otp/send', [OTPController::class, 'sendOTP']);
Route::post('otp/verify', [OTPController::class, 'verifyOTP']);
Route::post('otp/firebase/verify', [OTPController::class, 'verifyFirebaseToken']);
Route::post('login', [AuthController::class, 'login']);
Route::post('social/login', [SocialLoginController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('logout', [AuthController::class, 'logout']);

Route::get('verify/phone', [AuthController::class, 'verifyPhoneAccount']);
Route::post('password/reset/init', [AuthController::class, 'passwordReset']);
// Route::get('password/update/{code}/{email}', PasswordResetLivewire::class)->name('password.reset.link');

Route::get('categories', [CategoryController::class, "index"]);
Route::get('banners', [BannerController::class, "index"]);
Route::apiResource('products', ProductController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('vendors', VendorController::class);
Route::get('vendor/reviews', [ReviewController::class, 'index']);
Route::apiResource('vendor/types', VendorTypeController::class);
Route::get('coupons/{code}', [CouponController::class, 'show']);
Route::get('search', [SearchController::class, 'index']);

//package delivery
Route::get('package/types', [PackageTypeController::class, 'index']);
//
Route::post('order/payment/callback', [OrderPaymentCallbackController::class, 'order'])->name('api.payment.callback');
Route::post('wallet/topup/callback', [OrderPaymentCallbackController::class, 'wallet'])->name('api.wallet.topup.callback');
Route::post('subscription/callback', [OrderPaymentCallbackController::class, 'subscription'])->name('api.subscription.callback');
Route::apiResource('payment/methods', PaymentMethodController::class)->only('index');


Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::apiResource('favourites', FavouriteController::class);
    Route::get('my/profile', [UserController::class, 'myProfile']);
    Route::put('profile/update', [AuthController::class, 'profileUpdate']);
    Route::put('profile/password/update', [AuthController::class, 'changePassword']);
    Route::apiResource('delivery/addresses', DeliveryAddressController::class);
    Route::apiResource('orders', OrderController::class)->only('index', 'store', 'show', 'update');
    Route::post('/track/order', [TrackOrderController::class, "track"]);
    Route::apiResource('rating', RatingController::class)->only('store');
    //package delivery
    Route::get('package/order/summary', [PackageOrderController::class, 'summary']);
    Route::get('general/order/summary', [RegularOrderController::class, 'summary']);
    //
    Route::post('chat/notification', [ChatNotificationController::class, 'send']);

    //earning
    Route::get('earning/user', [EarningController::class, 'user']);
    Route::get('earning/vendor', [EarningController::class, 'vendor']);
    Route::get('users', [UserController::class, 'index']);
    Route::get('vendor/{id}/details', [VendorController::class, 'fullDeatils']);

    //wallets
    Route::get('wallet/balance', [WalletController::class, 'index']);
    Route::post('wallet/topup', [WalletController::class, 'topup']);
    Route::get('wallet/transactions', [WalletController::class, 'transactions']);
    Route::post('wallet/transfer', [WalletController::class, 'transferBalance']);

    //taxi booking
    Route::get('vehicle/types', [VehicleTypeController::class, 'index']);
    Route::get('vehicle/types/pricing', [VehicleTypeController::class, 'calculateFee']);
    Route::post('taxi/book/order', [TaxiOrderController::class, 'book']);    
    Route::get('taxi/current/order', [TaxiOrderController::class, 'current']);    
    Route::get('taxi/order/cancel/{id}', [TaxiOrderController::class, 'cancelOrder']);    
    Route::get('taxi/driver/info/{id}', [TaxiOrderController::class, 'driverInfo']);    
    Route::post('taxi/order/asignment/reject', [TaxiOrderController::class, 'driverRejectAssignment']);    

    //Payments
    Route::apiResource('payment/accounts', PaymentAccountController::class);
    Route::apiResource('payouts/request', PayoutController::class)->only('store');

    //
    Route::group(['middleware' => ['role:manager']], function () {

        Route::post('availability/vendor/{id}', [VendorController::class, 'toggleVendorAvailablity']);
        Route::apiResource('/vendor/package/pricing', VendorPackageTypePricingController::class);
        Route::apiResource('/my/services', VendorServiceController::class);
    });
});
