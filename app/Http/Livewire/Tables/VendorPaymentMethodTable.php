<?php

namespace App\Http\Livewire\Tables;


use App\Models\PaymentMethod;
use App\Models\VendorPaymentMethod;
use Kdion4891\LaravelLivewireTables\Column;

class VendorPaymentMethodTable extends BaseTableComponent
{

    public $model = VendorPaymentMethod::class;
    public $header_view = 'components.buttons.new';
    public $sort_attribute = 'payment_method.id';

    public function query()
    {
        return VendorPaymentMethod::with('payment_method')->where('vendor_id', \Auth::user()->vendor_id)->orderBy('payment_method_id');
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),'payment_method.id'),
            Column::make(__('Name'),'payment_method.name')->searchable(),
            Column::make(__('Actions'))->view('components.buttons.delete'),
        ];
    }

    public function initiateDelete($id){
        $this->selectedModel = $id;

        $this->confirm('Delete', [
            'toast' => false,
            'text' =>  __('Are you sure you want to delete the selected data?'),
            'position' => 'center',
            'showConfirmButton' => true,
            'cancelButtonText' => __("Cancel"),
            'onConfirmed' => 'deleteModel',
            'onCancelled' => 'cancelled'
        ]);
    }

    public function deleteModel(){

        try{
            $this->isDemo();
            \Auth::user()->vendor->payment_methods()->detach($this->selectedModel);
            $this->showSuccessAlert(__("Deleted"));
        }catch(Exception $error){
            $this->showErrorAlert( $error->getMessage() ?? "Failed");
        }
    }
}
