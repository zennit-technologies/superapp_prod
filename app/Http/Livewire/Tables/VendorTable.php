<?php

namespace App\Http\Livewire\Tables;

use App\Models\CountryVendor;
use App\Models\OrderProduct;
use App\Models\OrderService;
use App\Models\OrderStop;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\PackageTypePricing;
use App\Models\Product;
use App\Models\Service;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VendorTable extends BaseDataTableComponent
{

    public $model = Vendor::class;
    public bool $columnSelect = false;

    public function query()
    {
        return Vendor::with('vendor_type')->mine()->orderBy('id', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make(__('ID'), 'id')->searchable()->sortable(),
            Column::make(__('Name'), 'name')->searchable()->sortable(),
            Column::make(__('Type'), 'vendor_type.name'),
            Column::make(__('Active'))->format(function ($value, $column, $row) {
                return view('components.table.active', $data = [
                    "model" => $row
                ]);
            }),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->format(function ($value, $column, $row) {
                return view('components.buttons.market_actions', $data = [
                    "model" => $row
                ]);
            }),
        ];
    }

    //
    public function deleteModel()
    {

        try {
            $this->isDemo();
            \DB::beginTransaction();
            //
            $orderIds = Order::whereIn('vendor_id', [$this->selectedModel->id])->get()->pluck('id');
            //
            if (!empty($orderIds)) {
                //order_products
                OrderProduct::whereIn('order_id', $orderIds)->delete();
                //order_services
                OrderService::whereIn('order_id', $orderIds)->delete();
                //order_stops
                OrderStop::whereIn('order_id', $orderIds)->delete();
                //delete orders placed with that vendor
                Order::whereIn('vendor_id', [$this->selectedModel->id])->delete();
            }

            //products/services/packache type pricing
            $vendorProductIds = Product::where('vendor_id', $this->selectedModel->id)->pluck("id")->toArray();
            //delete any row in tbale that has vendor_id column
            $this->deleteFromTables('product_id', $vendorProductIds, true);
            $vendorServiceIds = Service::where('vendor_id', $this->selectedModel->id)->pluck("id")->toArray();
            //delete any row in tbale that has vendor_id column
            $this->deleteFromTables('service_id', $vendorServiceIds, true);
            $vendorPackageTypePricingIds = PackageTypePricing::where('vendor_id', $this->selectedModel->id)->pluck("id")->toArray();
            //delete any row in tbale that has vendor_id column
            $this->deleteFromTables('package_type_pricing_id', $vendorPackageTypePricingIds, true);


            //
            CountryVendor::where('vendor_id', $this->selectedModel->id)->delete();

            //delete any row in tbale that has vendor_id column
            $this->deleteFromTables('vendor_id', $this->selectedModel->id, false, ['users']);

            $this->selectedModel = $this->selectedModel->fresh();
            $this->selectedModel->forceDelete();

            \DB::commit();
            $this->showSuccessAlert("Deleted");
        } catch (Exception $error) {
            \DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? "Failed");
        }
    }


    //
    function deleteFromTables($column, $modelId, $isArray = false, $excludedTables = [])
    {
        $tables = \DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            foreach ($table as $key => $value)
                if (!in_array($value, $excludedTables)) {
                    if (Schema::hasColumn($value, $column)) {
                        if ($isArray) {
                            \DB::table($value)->whereIn($column, $modelId)->delete();
                        } else {
                            \DB::table($value)->where($column, $modelId)->delete();
                        }
                    }
                }
        }
    }
}
