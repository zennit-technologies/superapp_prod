<?php

namespace App\Providers;

use App\Listeners\OrderStatusEventSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Models\User;
use App\Observers\UserObserver;
use App\Models\Order;
use App\Models\PackageType;
use App\Models\SubscriptionVendor;
use App\Models\Vehicle;
use App\Models\Payout;
use App\Models\Product;
use App\Models\Service;
//
use App\Observers\OrderObserver;
use App\Observers\PackageTypeObserver;
use App\Observers\SubscriptionObserver;
//
use App\Observers\TaxiDriverObserver;
use App\Observers\TaxiOrderObserver;
use App\Observers\VehicleObserver;
use App\Observers\PayoutObserver;
use App\Observers\ProductObserver;
use App\Observers\ServiceObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    protected $subscribe = [
        OrderStatusEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
        User::observe(UserObserver::class);
        Order::observe(OrderObserver::class);
        SubscriptionVendor::observe(SubscriptionObserver::class);
        Payout::observe(PayoutObserver::class);

        //Majorly for taxi 
        User::observe(TaxiDriverObserver::class);
        Order::observe(TaxiOrderObserver::class);
        Vehicle::observe(VehicleObserver::class);

        //Subscription qty checks
        Product::observe(ProductObserver::class);
        Service::observe(ServiceObserver::class);
        PackageType::observe(PackageTypeObserver::class);
    }
}
