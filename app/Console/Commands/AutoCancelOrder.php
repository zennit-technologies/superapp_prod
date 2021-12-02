<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoCancelOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel pending order when the time is right';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //
        if(setting('autoCancelPendingOrderTime', 30) == "0"){
            return;
        }
        //get orders pending for more the ``autoCancelPendingOrderTime``
        $orders = Order::currentStatus('pending')->whereDoesntHave('taxi_order')->whereRaw('updated_at <= now() - interval '.setting('autoCancelPendingOrderTime', 30).' minute')->limit(20)->get();

        foreach ($orders as $order) {
            $order->setStatus('cancelled');
        }
    }
}
