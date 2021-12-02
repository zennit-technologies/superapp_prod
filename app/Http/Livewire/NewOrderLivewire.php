<?php

namespace App\Http\Livewire;

use App\Models\Coupon;
use App\Models\DeliveryAddress;
use App\Models\Option;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Models\CouponUser;
use App\Models\Vendor;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NewOrderLivewire extends BaseLivewireComponent
{

    protected $listeners = [
        'showCreateModal' => 'showCreateModal',
        'showEditModal' => 'showEditModal',
        'showDetailsModal' => 'showDetailsModal',
        'showAssignModal' => 'showAssignModal',
        'initiateEdit' => 'initiateEdit',
        'initiateDelete' => 'initiateDelete',
        'removeModel' => 'removeModel',
        'initiateAssign' => 'initiateAssign',
        'initiateSubcategoriesAssign' => 'initiateSubcategoriesAssign',
        'initiatePayout' => 'initiatePayout',
        'dismissModal' => 'dismissModal',
        'refreshView' => '$refresh',
        'select2Change' => 'select2Change',
        'productsChange' => 'productsChange',
        'vendorsChange' => 'vendorsChange',
        'managersChange' => 'managersChange',
        'paymentMethodsChange' => 'paymentMethodsChange',
        'categoriesChange' => 'categoriesChange',
        'vendorChange' => 'vendorChange',
        'changeVendorTiming' => 'changeVendorTiming',
        'changeFCMToken' => 'changeFCMToken',
        'logout' => 'logout',
        'reviewPayment' => 'reviewPayment',
        'customerChange' => 'customerChange',
        'deliveryAddressesChange' => 'deliveryAddressesChange',
        'autocompleteAddressSelected' => 'autocompleteAddressSelected',
        'autocompleteUserSelected' => 'autocompleteUserSelected',
        'photoSelected' => 'photoSelected',
        'autocompleteDriverSelected' => 'autocompleteDriverSelected',
        'autocompleteVendorSelected' => 'autocompleteVendorSelected',
        'autocompleteProductSelected' => 'autocompleteProductSelected',
        'autocompleteDeliveryAddressSelected' => 'autocompleteDeliveryAddressSelected',
    ];


    public $showSummary = false;
    public $vendorID;
    public $vendor;
    public $product;
    public $productIDs = [];
    public $paymentMethods;
    public $newOrderProducts;
    public $newProductOptions = [];
    public $newProductSelectedOptions;
    public $newOrderProductsQtys;
    public $couponCode;
    public $isPickup;
    public $tip;
    public $userId;
    public $deliveryAddressId;
    public $paymentMethodId;
    public $coupon;
    public $newOrder;

    public $vendors;
    public $products;
    public $vendorSearchClause = [];
    public $productSearchClause = ['vendor_id' => 0];
    public $addressSearchClause = ['user_id' => 0];


    public function mount()
    {
        //
        if (\Auth::user()->hasRole('manager')) {
            $this->vendorSearchClause = [
                "id" => \Auth::user()->vendor_id
            ];
        }
    }

    // New order
    public function showCreateModal()
    {
        $this->loadCustomData();
        $this->showCreate = true;
    }

    //select actions
    public function autocompleteVendorSelected($vendor)
    {

        try {
            //clear old products
            if ($this->vendorID != $vendor['id']) {
                $this->productIDs = null;
                $this->newOrderProducts = null;
            }
            $this->vendorID = $vendor['id'];
            $this->vendor = $vendor['name'];
            $this->productSearchClause = ['vendor_id' => $this->vendorID];
            $this->emit('productQueryClasueUpdate', $this->productSearchClause);
            $this->vendors = [];
        } catch (\Exception $ex) {
            logger("Error", [$ex]);
        }
    }

    public function autocompleteProductSelected($product)
    {
        try {

            if (empty($this->productIDs)) {
                $this->productIDs = [];
            }

            // 
            $productId = $product['id'];
            $newProductIDs = $this->productIDs;
            if (!is_array($newProductIDs)) {
                $newProductIDs = [];
            }
            //if product already exists
            if (!in_array($productId, $newProductIDs)) {
                array_push($newProductIDs, $productId);
            }
            $this->productIDs = $newProductIDs;
            $this->newOrderProducts = Product::whereIn('id', $this->productIDs)->get();
            //
        } catch (\Exception $ex) {
            logger("Error", [$ex]);
        }
    }

    public function autocompleteUserSelected($user)
    {
        try {
            //clear old products
            if ($this->userId != $user['id']) {
                $this->deliveryAddressId = null;
            }
            $this->userId = $user['id'];
            $this->addressSearchClause = ['user_id' => $this->userId];
            $this->emit('addressQueryClasueUpdate', $this->addressSearchClause);
        } catch (\Exception $ex) {
            logger("Error", [$ex]);
        }
    }

    public function autocompleteDriverSelected($driver)
    {
        try {
            //clear old products
            $this->deliveryBoyId = $driver['id'];
        } catch (\Exception $ex) {
            logger("Error", [$ex]);
        }
    }

    public function autocompleteDeliveryAddressSelected($deliveryAddress)
    {
        try {

            $this->deliveryAddressId = $deliveryAddress["id"];
        } catch (\Exception $ex) {
            logger("Error", [$ex]);
        }
    }



    public function removeModel($id)
    {
        //
        $this->newOrderProducts = $this->newOrderProducts->reject(function ($element) use ($id) {
            return $element->id == $id;
        });

        //
        $this->productIDs = $this->newOrderProducts->pluck('id') ?? [];
        $this->newOrderProductsQtys[$id] = null;
    }

    public function applyDiscount()
    {

        $this->coupon = Coupon::with('vendors', 'products')->active()->where('code', $this->couponCode)->first();
        if (empty($this->coupon)) {
            $this->addError('couponCode', __('Invalid Coupon Code'));
        } else {
            $this->resetValidation('couponCode');
        }
    }

    public function showOrderSummary()
    {
        //
        if (empty($this->vendorID)) {
            $this->showErrorAlert(__("Please check Vendor"));
            return;
        } else if (empty($this->productIDs)) {
            $this->showErrorAlert(__("Please check at least one product"));
            return;
        } else if (empty($this->userId)) {
            $this->showErrorAlert(__("Please check customer"));
            return;
        } else if (!$this->isPickup && empty($this->deliveryAddressId)) {
            $this->showErrorAlert(__("Please select delivery address"));
            return;
        } else if (!$this->isPickup) {
            //disctance between vendor and delivery address
            $vendor = Vendor::find($this->vendorID);
            //default delivery address
            $deliveryAddress = DeliveryAddress::distance($vendor->latitude, $vendor->longitude)->find($this->deliveryAddressId);
            if ($deliveryAddress->distance > $vendor->delivery_range) {
                $this->showErrorAlert(__(("Delivery address is out of vendor delivery range")));
                return;
            }

        }


        //
        $this->validate([
            'newOrderProductsQtys.*' => 'required|numeric|min:1',
        ], [
            'newOrderProductsQtys.*' => __('Qty is required'),
        ]);


        //
        $this->newOrder = $this->getOrderData();
        $this->showSummary = true;
    }

    public function saveNewOrder()
    {

        //
        try {
            DB::beginTransaction();
            $this->newOrder = $this->getOrderData();
            $this->newOrder->save();
            $this->newOrder->setStatus("pending");

            foreach ($this->newOrderProducts as $newOrderProduct) {
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $this->newOrder->id;
                $orderProduct->quantity = ($this->newOrderProductsQtys[$newOrderProduct->id] ?? 1);
                $orderProduct->price = ($newOrderProduct->discount_price <= 0) ? $newOrderProduct->price : $newOrderProduct->discount_price;
                $orderProduct->product_id = $newOrderProduct->id;

                //flatten options
                $productOptionsString = "";
                $productOptionsIds = "";
                if (!empty($this->newProductSelectedOptions) && !empty($this->newProductSelectedOptions[$newOrderProduct->id])) {
                    $productOptions = $this->newProductSelectedOptions[$newOrderProduct->id];
                    foreach ($productOptions as $key => $productOption) {
                        $productOptionsString .= $productOption->name;
                        $productOptionsIds .= $productOption->id;
                        if ($key < (count($productOptions) - 1)) {
                            $productOptionsString .= ", ";
                            $productOptionsIds .= ",";
                        }
                    }
                }
                //
                $orderProduct->options = $productOptionsString;
                $orderProduct->options_ids = $productOptionsIds;
                $orderProduct->save();

                //reduce product qty
                $product = $orderProduct->product;
                if (!empty($product->available_qty)) {
                    $product->available_qty = $product->available_qty - $orderProduct->quantity;
                    $product->save();
                }
            }

            //save the coupon used
            $coupon = Coupon::where("code", $this->couponCode)->first();
            if (!empty($coupon)) {
                $couponUser = new CouponUser();
                $couponUser->coupon_id = $coupon->id;
                $couponUser->user_id = \Auth::id();
                $couponUser->order_id = $this->newOrder->id;
                $couponUser->save();
            }

            //so apps can be notified 
            $this->newOrder->updated_at = \Carbon\Carbon::now();
            $this->newOrder->save();

            DB::commit();
            $this->showSuccessAlert(__("New Order successfully!"));

            $this->showSummary = false;
            $this->showCreate = false;
            $this->reset();
            $this->emit('clearAutocompleteFieldsEvent');
            $this->emit('refreshTable');
        } catch (\Exception $ex) {
            DB::rollback();
            $this->showErrorAlert($ex->getMessage() ?? __("New Order failed!"));
        }
    }


    //get order
    public function getOrderData()
    {

        $deliveryFee = 0;
        $order = new Order();
        $order->vendor_id = $this->vendorID;
        $order->user_id = $this->userId;
        $order->delivery_address_id = $this->deliveryAddressId;
        if (empty($this->paymentMethodId)) {
            $order->payment_method_id = $this->paymentMethods->first()->id;
        } else {
            $order->payment_method_id = $this->paymentMethodId;
        }

        //cash payment
        if ($order->payment_method->slug == "cash") {
            $order->payment_status = "successful";
        }
        $order->tip = $this->tip;
        $order->note = $this->note;
        $order->created_at = Carbon::now();
        $order->updated_at = Carbon::now();

        //
        foreach ($this->newOrderProducts as $key => $newOrderProduct) {
            if ($newOrderProduct->discount_price > 0) {
                $productPrice = $newOrderProduct->discount_price;
            } else {
                $productPrice = $newOrderProduct->price;
            }
            $order->sub_total += $productPrice * ($this->newOrderProductsQtys[$newOrderProduct->id] ?? 1);
        }

        foreach ($this->newProductOptions ?? [] as $key => $newProductOptionObject) {

            $optionIdsArray = [];
            foreach ($newProductOptionObject as $newProductOptionObjectValues) {

                //
                if (gettype($newProductOptionObjectValues) == 'array') {
                    foreach ($newProductOptionObjectValues as $key3 => $newProductOptionObjectValue) {
                        if ($newProductOptionObjectValue) {
                            array_push($optionIdsArray, $key3);
                        }
                    }
                } else {
                    array_push($optionIdsArray, $newProductOptionObjectValues);
                }
            }

            $selectedProductOptions = Option::whereIn('id', $optionIdsArray)->get();
            $this->newProductSelectedOptions[$key] = $selectedProductOptions;

            //pricing
            foreach ($selectedProductOptions as $selectedProductOption) {
                $order->sub_total += $selectedProductOption->price;
            }
        }


        //
        if (!empty($this->coupon)) {
            //
            $couponVendors = $this->coupon->vendors;
            $couponVendorsIds = $this->coupon->vendors->pluck('id')->toArray();
            $couponProducts = $this->coupon->products;
            $couponProductsIds = $this->coupon->products->pluck('id')->toArray();

            //apply discount directly to total order
            if (count($couponVendors) == 0 && count($couponProducts) == 0) {

                if ($this->coupon->percentage) {
                    $order->discount = $order->sub_total * ($this->coupon->discount / 100);
                } else {
                    $order->discount = $this->coupon->discount;
                }
            } else if (count($couponProducts) > 0) {
                //go through selected products
                foreach ($this->newOrderProducts as $key => $newOrderProduct) {
                    if ($newOrderProduct->discount_price > 0) {
                        $productPrice = $newOrderProduct->discount_price;
                    } else {
                        $productPrice = $newOrderProduct->price;
                    }
                    //if the current product in loop is in the products coupon can be applied on
                    if (in_array($newOrderProduct->id, $couponProductsIds)) {
                        if ($this->coupon->percentage) {
                            $order->discount += $productPrice * ($this->coupon->discount / 100);
                        } else {
                            $order->discount += $productPrice * $this->coupon->discount;
                        }
                    }
                }
            } else if (count($couponVendors) > 0) {
                //check if vendor is part of listed vendors coupon can be applied
                if (in_array($this->newOrder->vendor_id, $couponVendorsIds)) {
                    if ($this->coupon->percentage) {
                        $order->discount = $order->sub_total * ($this->coupon->discount / 100);
                    } else {
                        $order->discount = $order->sub_total * $this->coupon->discount;
                    }
                }
            } else {
                $order->discount = 0;
            }
        } else {
            $order->discount = 0;
        }


        //delivery fee
        if(!$this->isPickup){
            $vendor = Vendor::find($this->vendorID);
            $deliveryAddress = DeliveryAddress::distance($vendor->latitude, $vendor->longitude)->find($this->deliveryAddressId);

            $deliveryFee = $vendor->base_delivery_fee;
            $deliveryFee += $vendor->charge_per_km ? ($vendor->delivery_fee * $deliveryAddress->distance) : $vendor->delivery_fee;
            $deliveryFee = 300;
            
        }

        $order->sub_total = number_format($order->sub_total, 2, '.', '');
        $order->delivery_fee = number_format($deliveryFee, 2, '.', '');
        $order->discount = number_format($order->discount, 2, '.', '');
        $order->tip = number_format($order->tip, 2, '.', '');
        $order->tax = number_format($order->sub_total * ($order->vendor->tax / 100), 2, '.', '');
        $order->total = $order->sub_total - $order->discount + $order->tax + $order->tip + $order->delivery_fee;
        return $order;
    }
}
