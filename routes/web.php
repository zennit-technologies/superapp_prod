<?php

use App\Http\Livewire\Auth\LoginLivewire;
use App\Http\Livewire\Auth\RegisterLivewire;
use App\Http\Livewire\Auth\PasswordResetLivewire;
use App\Http\Livewire\Auth\ForgotPasswordLivewire;

use App\Http\Livewire\BannerLivewire;
use App\Http\Livewire\DashboardLivewire;
use App\Http\Livewire\CategoryLivewire;
use App\Http\Livewire\SubCategoryLivewire;
use App\Http\Livewire\VendorTypeLivewire;
use App\Http\Livewire\VendorLivewire;
use App\Http\Livewire\ProductLivewire;
use App\Http\Livewire\FavouriteLivewire;
use App\Http\Livewire\ReviewLivewire;
use App\Http\Livewire\OptionGroupLivewire;
use App\Http\Livewire\MenuLivewire;
use App\Http\Livewire\OptionLivewire;
use App\Http\Livewire\WalletTransactionLivewire;
use App\Http\Livewire\PaymentAccountLivewire;

use App\Http\Livewire\ServiceLivewire;

use App\Http\Livewire\OrderLivewire;
use App\Http\Livewire\CouponLivewire;
use App\Http\Livewire\DeliveryAddressLivewire;

use App\Http\Livewire\CurrencyLivewire;
use App\Http\Livewire\AppSettingsLivewire;
use App\Http\Livewire\WebsiteSettingsLivewire;
use App\Http\Livewire\ServerSettingsLivewire;
use App\Http\Livewire\SettingsLivewire;
use App\Http\Livewire\PaymentMethodivewire;
use App\Http\Livewire\VendorPaymentMethodLivewire;
use App\Http\Livewire\Payment\OrderPaymentLivewire;
use App\Http\Livewire\Payment\OrderPaymentCallbackLivewire;

use App\Http\Livewire\PackageTypeLivewire;
use App\Http\Livewire\PackageTypePricingLivewire;
use App\Http\Livewire\CountryLivewire;
use App\Http\Livewire\StateLivewire;
use App\Http\Livewire\CitiesLivewire;
use App\Http\Livewire\VendorCitiesLivewire;
use App\Http\Livewire\VendorStatesLivewire;
use App\Http\Livewire\VendorCountriesLivewire;

use App\Http\Livewire\UserLivewire;
use App\Http\Livewire\DriverLivewire;
use App\Http\Livewire\DriverEarningLivewire;
use App\Http\Livewire\DriverRemittanceLivewire;
use App\Http\Livewire\VendorEarningLivewire;
use App\Http\Livewire\PayoutLivewire;
use App\Http\Livewire\MyPayoutLivewire;

use App\Http\Livewire\BackUpLivewire;
use App\Http\Livewire\DataLivewire;
use App\Http\Livewire\NotificationLivewire;
use App\Http\Livewire\TranslationLivewire;
use App\Http\Livewire\ImportLivewire;
use App\Http\Livewire\UpgradeLivewire;
use App\Http\Livewire\SMSGatewayLivewire;
use App\Http\Livewire\ExtensionLivewire;
use App\Http\Livewire\CronJobLivewire;
use App\Http\Livewire\AutoAssignmentLivewire;
use App\Http\Livewire\TroubleShootLivewire;

use App\Http\Livewire\Payment\WalletTopUpLivewire;
use App\Http\Livewire\Payment\WalletTopUpCallbackLivewire;

use App\Http\Livewire\SubscriptionLivewire;
use App\Http\Livewire\SubscribeLivewire;
use App\Http\Livewire\MySubscriptionLivewire;
use App\Http\Livewire\VendorSubscriptionLivewire;
use App\Http\Livewire\Payment\SubscribeCallbackLivewire;

use App\Http\Livewire\ProfileLivewire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

//
use App\Http\Livewire\VehicleTypeLivewire;
use App\Http\Livewire\VehicleLivewire;
use App\Http\Livewire\CarMakeLivewire;
use App\Http\Livewire\CarModelLivewire;
use App\Http\Livewire\PaymentMethodVehicleTypeLivewire;
use App\Http\Livewire\TaxiSettingLivewire;
use App\Http\Livewire\TaxiPricingLivewire;
use App\Http\Livewire\DeliveryZoneLivewire;

//Reports
use App\Http\Livewire\Report\CouponReportLivewire;
use App\Http\Livewire\Report\ProductReportLivewire;
use App\Http\Livewire\Report\ServiceReportLivewire;
use App\Http\Livewire\Report\VendorReportLivewire;
use App\Http\Livewire\Report\SubscriptionReportLivewire;
use App\Http\Livewire\Report\CustomerReportLivewire;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['web']], function () {

    // Auth
    Route::get('login', LoginLivewire::class)->name('login');
    Route::get('register', RegisterLivewire::class)->name('register');
    Route::get('logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');

    Route::get('password/forgot', ForgotPasswordLivewire::class)->name('password.forgot');
    Route::get('password/update/{code}/{email}', PasswordResetLivewire::class)->name('password.reset.link');


    // Pages
    Route::get('privacy/policy', function () {
        return view('layouts.pages.privacy');
    })->name('privacy');

    Route::get('pages/contact', function () {
        return view('layouts.pages.contact');
    })->name('contact');

    Route::get('pages/terms', function () {
        return view('layouts.pages.terms');
    })->name('terms');

    // AUth routes
    Route::group(['middleware' => ['auth']], function () {

        //
        Route::get('profile', ProfileLivewire::class)->name('profile');
        Route::get('', DashboardLivewire::class)->name('dashboard');
        Route::get('product/products', ProductLivewire::class)->name('products');
        Route::get('product/menus', MenuLivewire::class)->name('products.menus');
        Route::get('product/options/group', OptionGroupLivewire::class)->name('products.options.group');
        Route::get('product/options', OptionLivewire::class)->name('products.options');

        //services 
        Route::get('service/services', ServiceLivewire::class)->name('services');

        Route::get('order/orders', OrderLivewire::class)->name('orders');
        Route::get('order/coupons', CouponLivewire::class)->name('coupons');

        Route::get('vendors/types', VendorTypeLivewire::class)->name('vendor.types');
        Route::get('vendors', VendorLivewire::class)->name('vendors');

        //admin/manager routes
        Route::get('earnings/drivers', DriverEarningLivewire::class)->name('earnings.drivers');
        Route::get('earnings/remittance', DriverRemittanceLivewire::class)->name('earnings.remittance');
        Route::get('payouts', PayoutLivewire::class)->name('payouts');
        Route::get('payments/accounts', PaymentAccountLivewire::class)->name('payment.accounts');


        //admin routes
        Route::group(['middleware' => ['role:admin']], function () {

            //
            Route::get('operations/cron/job', CronJobLivewire::class)->name('configure.cron.job');
            Route::get('operations/order/assignment', AutoAssignmentLivewire::class)->name('auto.assignments');
            Route::get('operations/troubleshooting', TroubleShootLivewire::class)->name('troubleshooting');

            Route::get('banners', BannerLivewire::class)->name('banners');
            Route::get('categories', CategoryLivewire::class)->name('categories');
            Route::get('categories/subcategories', SubCategoryLivewire::class)->name('subcategories');
            Route::get('product/favourites', FavouriteLivewire::class)->name('favourites');
            Route::get('order/reviews', ReviewLivewire::class)->name('reviews');
            // Route::get('order/delivery/addresses', DeliveryAddressLivewire::class)->name('delivery.addresses');

            Route::get('payments/wallet/transactions', WalletTransactionLivewire::class)->name('wallet.transactions');
            
            //
            Route::get('setting/currencies', CurrencyLivewire::class)->name('currencies');
            Route::get('setting/settings', SettingsLivewire::class)->name('settings');
            Route::get('setting/app/settings', AppSettingsLivewire::class)->name('settings.app');
            Route::get('setting/website/settings', WebsiteSettingsLivewire::class)->name('settings.website');
            Route::get('setting/app/server', ServerSettingsLivewire::class)->name('settings.server');
            Route::get('setting/payment/methods', PaymentMethodivewire::class)->name('payment.methods');
            Route::get('setting/translation', TranslationLivewire::class)->name('translation');
            Route::get('setting/upgrade', UpgradeLivewire::class)->name('upgrade');

            //package
            Route::get('package/types', PackageTypeLivewire::class)->name('package.types');
            Route::get('package/countries', CountryLivewire::class)->name('package.countries');
            Route::get('package/states', StateLivewire::class)->name('package.states');
            Route::get('package/cities', CitiesLivewire::class)->name('package.cities');


            //imports
            Route::get('operations/notification/send', NotificationLivewire::class)->name('notification.send');
            Route::get('operations/imports', ImportLivewire::class)->name('imports');
            Route::get('operations/backup', BackUpLivewire::class)->name('backups');
            Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('logs');
            Route::get('operations/data/clear', DataLivewire::class)->name('data.clear');

            //settings
            Route::get('setting/sms/gateways', SMSGatewayLivewire::class)->name('sms.settings');
            //subscription
            Route::get('subscription/subscriptions', SubscriptionLivewire::class)->name('subscriptions');
            Route::get('reports/subscriptions', SubscriptionReportLivewire::class)->name('reports.subscriptions');

            //Taxi booking
            Route::get('taxi/vehicle/types', VehicleTypeLivewire::class)->name('taxi.vehicle.types');
            Route::get('taxi/vehicles', VehicleLivewire::class)->name('taxi.vehicles');
            Route::get('taxi/car/makes', CarMakeLivewire::class)->name('taxi.car.makes');
            Route::get('taxi/car/models', CarModelLivewire::class)->name('taxi.car.models');
            Route::get('taxi/payment/methods', PaymentMethodVehicleTypeLivewire::class)->name('taxi.payment.methods');
            Route::get('taxi/settings', TaxiSettingLivewire::class)->name('taxi.settings');
            Route::get('taxi/pricing', TaxiPricingLivewire::class)->name('taxi.pricing');


            //Location Zone mangt. 
            Route::get('vendors/zones', DeliveryZoneLivewire::class)->name('zones');
        });

        Route::group(['middleware' => ['role:admin|city-admin']], function () {

            Route::get('users', UserLivewire::class)->name('users');
            Route::get('order/delivery/addresses', DeliveryAddressLivewire::class)->name('delivery.addresses');
            Route::get('earnings/vendors', VendorEarningLivewire::class)->name('earnings.vendors');
            Route::get('extensions', ExtensionLivewire::class)->name('extensions');

            //subscription
            Route::get('subscription/vendors/subscriptions', VendorSubscriptionLivewire::class)->name('vendors.subscriptions');


            //report
            Route::get('reports/coupons', CouponReportLivewire::class)->name('reports.coupons');
        });

        //manager routes
        Route::group(['middleware' => ['role:manager']], function () {

            Route::get('package/pricing', PackageTypePricingLivewire::class)->name('package.pricing');
            Route::get('package/my/cities', VendorCitiesLivewire::class)->name('package.cities.my');
            Route::get('package/my/states', VendorStatesLivewire::class)->name('package.states.my');
            Route::get('package/my/countries', VendorCountriesLivewire::class)->name('package.countries.my');
            Route::get('drivers', DriverLivewire::class)->name('drivers');
            Route::get('vendor/payment/methods', VendorPaymentMethodLivewire::class)->name('payment.methods.my');
            //subscription
            Route::get('subscription/my', MySubscriptionLivewire::class)->name('my.subscriptions');
            Route::get('subscription/my/subscribe', SubscribeLivewire::class)->name('my.subscribe');
            
            //
            Route::get('service/my/cities', VendorCitiesLivewire::class)->name('service.cities.my');
            Route::get('service/my/states', VendorStatesLivewire::class)->name('service.states.my');
            Route::get('service/my/countries', VendorCountriesLivewire::class)->name('service.countries.my');
            //Payouts
            Route::get('payments/my/payouts', MyPayoutLivewire::class)->name('my.payouts');
        });


        //report
        //Reports
        Route::get('reports/products', ProductReportLivewire::class)->name('reports.products');
        Route::get('reports/services', ServiceReportLivewire::class)->name('reports.services');
        Route::get('reports/vendors', VendorReportLivewire::class)->name('reports.vendors');
        Route::get('reports/customers', CustomerReportLivewire::class)->name('reports.customers');
        
    });



    //Unauth routes
    Route::get('order/payment', OrderPaymentLivewire::class)->name('order.payment');
    Route::get('order/payment/callback', OrderPaymentCallbackLivewire::class)->name('payment.callback');
    //Wallet
    Route::get('wallet/topup', WalletTopUpLivewire::class)->name('wallet.topup');
    Route::get('wallet/topup/callback', WalletTopUpCallbackLivewire::class)->name('wallet.topup.callback');

    //Subscription callback
    Route::get('subscription/payment/callback', SubscribeCallbackLivewire::class)->name('subscription.callback');
});
