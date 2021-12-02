<?php

namespace App\Http\Livewire\Tables;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProductTable extends BaseDataTableComponent
{

    public $model = Product::class;
    public bool $columnSelect = false;

    public function query()
    {

        $user = User::find(Auth::id());
        if ($user->hasRole('admin')) {
            return Product::query();
        } elseif ($user->hasRole('city-admin')) {
            return Product::with('vendor')->whereHas("vendor", function ($query) {
                return $query->where('creator_id', Auth::id());
            });
        } else {
            return Product::where("vendor_id", Auth::user()->vendor_id);
        }
    }
    public function columns(): array
    {
        return [
            Column::make(__('ID'), 'id')->searchable()->sortable(),
            Column::make(__('Image'))->format(function ($value, $column, $row) {
                return view('components.table.image_sm', $data = [
                    "model" => $row
                ]);
            }),
            Column::make(__('Name'), 'name')->addClass('break-all p-2 w-64 md:w-2/12')->searchable()->sortable(),
            Column::make(__('Price'),'price')->format(function ($value, $column, $row) {
                return view('components.table.price', $data = [
                    "model" => $row
                ]);
            })->searchable()->sortable(),
            Column::make(__('Discount Price'),'discount_price')->format(function ($value, $column, $row) {
                return view('components.table.discount_price', $data = [
                    "model" => $row
                ]);
            })->searchable()->sortable(),
            Column::make(__('Available Qty'), "available_qty")->sortable(),
            Column::make(__('Active'))->format(function ($value, $column, $row) {
                return view('components.table.active', $data = [
                    "model" => $row
                ]);
            }),
            Column::make(__('Actions'))->format(function ($value, $column, $row) {
                return view('components.buttons.product_actions', $data = [
                    "model" => $row
                ]);
            }),


        ];
    }

    //     public function rowView(): string
    // {
    //      // Becomes /resources/views/location/to/my/row.blade.php
    //      return 'components.table.product-row';
    // }

    //
    public function deleteModel()
    {

        try {
            $this->isDemo();
            \DB::beginTransaction();
            $this->selectedModel->delete();
            \DB::commit();
            $this->showSuccessAlert("Deleted");
        } catch (Exception $error) {
            \DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? "Failed");
        }
    }
}
