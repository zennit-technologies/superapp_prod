<?php

namespace App\Http\Livewire;

use App\Models\Earning;
use App\Models\Payout;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Vendor;
use App\Traits\FirebaseDBTrait;

class DataLivewire extends BaseLivewireComponent
{

    use FirebaseDBTrait;
    public $model;
    public $actionCalled;

    public function render()
    {
        return view('livewire.settings.data-clear');
    }


    //
    public function confirmAction($model, $method)
    {
        $this->model = $model;
        $this->actionCalled  = $method;
        $this->showCreateModal();
    }

    public function clearOrders()
    {
        //
        try {

            $this->isDemo();
            
            DB::statement("SET foreign_key_checks=0");
            //order payments
            DB::table('payments')->truncate();
            DB::table('statuses')->truncate();
            //clear order products
            DB::table('order_products')->truncate();
            //order stops
            DB::table('order_stops')->truncate();
            //order taxi
            DB::table('taxi_orders')->truncate();
            //remittances
            DB::table('remittances')->truncate();
            //orders
            DB::table('orders')->truncate();
            DB::table('auto_assignments')->truncate();

            //
            $vendorEarningIds = Earning::whereNull("user_id")->pluck("id")->toArray();
            Payout::whereIn("earning_id", $vendorEarningIds)->delete();
            DB::table('earnings')->whereNull("user_id")->truncate();

            DB::statement("SET foreign_key_checks=1");
            
            $this->dismissModal();
            $this->showSuccessAlert($this->model . " " . __('clear successfully!'));
        } catch (Exception $error) {
            
            $this->showErrorAlert($error->getMessage() ?? $this->model . " " . __('clearing failed!'));
        }
    }

    public function clearProducts()
    {
        //
        try {

            $this->isDemo();
            
            DB::statement("SET foreign_key_checks=0");
            \DB::table('category_product')->truncate();
            \DB::table('coupon_product')->truncate();
            \DB::table('menu_product')->truncate();
            \DB::table('option_product')->truncate();
            \DB::table('product_subcategory')->truncate();
            \DB::table('favourites')->truncate();
            \DB::table('products')->truncate();
            //clear media
            $this->clearMedia(["App\Models\Product"]);
            DB::statement("SET foreign_key_checks=1");
            

            $this->dismissModal();
            $this->showSuccessAlert($this->model . " " . __('clear successfully!'));
        } catch (Exception $error) {
            
            $this->showErrorAlert($error->getMessage() ?? $this->model . " " . __('clearing failed!'));
        }
    }

    public function clearVendors()
    {
        //
        //
        try {

            $this->isDemo();
            

            //unassugn the managers
            User::whereNotNull('vendor_id')
                ->update(['vendor_id' => null]);

            DB::statement("SET foreign_key_checks=0");
            \DB::table('category_vendor')->truncate();
            \DB::table('country_vendor')->truncate();
            \DB::table('city_vendor')->truncate();
            \DB::table('day_vendor')->truncate();
            \DB::table('payment_method_vendor')->truncate();
            \DB::table('state_vendor')->truncate();
            \DB::table('package_type_pricings')->truncate();
            \DB::table('option_product')->delete();
            \DB::table('options')->truncate();
            \DB::table('option_groups')->truncate();
            //
            $vendorEarningIds = Earning::whereNull("user_id")->pluck("id")->toArray();
            Payout::whereIn("earning_id", $vendorEarningIds)->delete();
            \DB::table('earnings')->whereNull("user_id")->delete();
            //
            \DB::table('vendors')->truncate();
            //clear media
            $this->clearMedia(["App\Models\Vendor", "App\Models\PackageType", "App\Models\Option", "App\Models\OptionGroup"]);
            DB::statement("SET foreign_key_checks=1");
            

            $this->dismissModal();
            $this->showSuccessAlert($this->model . " " . __('clear successfully!'));
        } catch (Exception $error) {
            
            $this->showErrorAlert($error->getMessage() ?? $this->model . " " . __('clearing failed!'));
        }
    }

    public function clearUsers()
    {
        //
        try {

            $this->isDemo();
            

            //unassugn the managers
            DB::statement("SET foreign_key_checks=0");
            //
            User::where('creator_id', "!=", \Auth::id())
                ->update(['creator_id' => null]);
            //vendors
            Vendor::where('creator_id', "!=", \Auth::id())
                ->update(['creator_id' => null]);
            //
            \DB::table('delivery_addresses')->truncate();
            \DB::table('wallet_transactions')->truncate();
            \DB::table('wallets')->truncate();
            \DB::table('users')->where('id', "!=", \Auth::id())->delete();
            $this->clearMedia(["App\Models\User"]);
            DB::statement("SET foreign_key_checks=1");
            

            $this->dismissModal();
            $this->showSuccessAlert($this->model . " " . __('clear successfully!'));
        } catch (Exception $error) {
            
            $this->showErrorAlert($error->getMessage() ?? $this->model . " " . __('clearing failed!'));
        }
    }


    





    //clear media 
    public function clearMedia(array $models)
    {
        //clear media
        \DB::table('media')->whereIn("model_type", $models)->delete();
    }
}
