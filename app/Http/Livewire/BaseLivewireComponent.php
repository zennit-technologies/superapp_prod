<?php

namespace App\Http\Livewire;

use App\Models\UserToken;
use App\Models\Product;
use App\Models\Vendor;
use App\Traits\AutocompleteTrait;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Exception;

class BaseLivewireComponent extends Component
{
    use WithPagination, WithFileUploads;
    use AutocompleteTrait;

    public $perPage = 6;
    public $showPassword = false;
    public $model;
    public $selectedModel;
    public $photoInfo;
    public $secondPhotoInfo;
    public $photo;
    public $secondPhoto;

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
        'initiateEarningWalletTransfer' => 'initiateEarningWalletTransfer',
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
        'autocompleteDriverSelected' => 'autocompleteDriverSelected',
        'autocompleteAddressSelected' => 'autocompleteAddressSelected',
        'autocompleteProductSelected' => 'autocompleteProductSelected',
        'autocompleteVendorSelected' => 'autocompleteVendorSelected',
        'autocompleteUserSelected' => 'autocompleteUserSelected',
        'autocompleteCategorySelected' => 'autocompleteCategorySelected',
        'photoSelected' => 'photoSelected',
        'refreshDataTable' => 'refreshDataTable',
    ];

    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function refreshDataTable()
    {
        $this->emit('refreshTable');
    }

    //Alert
    public function showSuccessAlert($message = "", $time = 3000)
    {
        $this->alert('success', "", [
            'position'  =>  'center',
            'text' => $message,
            'toast'  =>  false,
            "timer" => $time,
        ]);
    }

    public function showWarningAlert($message = "", $time = 3000)
    {
        $this->alert('warning', "", [
            'position'  =>  'center',
            'text' => $message,
            'toast'  =>  false,
            "timer" => $time,
        ]);
    }

    public function showErrorAlert($message = "", $time = 3000)
    {
        $this->alert('error', "", [
            'position'  =>  'center',
            'text' => $message,
            'toast'  =>  false,
            "timer" => $time,
        ]);
    }

    public function showSelect2($selectorID, $data, $onChange, $options = null)
    {
        $this->emit('showSelect2', [
            $selectorID,
            $data,
            $onChange,
            $options
        ],);
    }


    // Modal management
    public $showCreate = false;
    public $showEdit = false;
    public $showDetails = false;
    public $showAssign = false;
    public $stopRefresh = false;
    public function showCreateModal()
    {
        $this->showCreate = true;
        $this->stopRefresh = true;
    }

    public function showEditModal()
    {
        $this->showEdit = true;
        $this->stopRefresh = true;
    }

    public function showDetailsModal($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->showDetails = true;
        $this->stopRefresh = true;
    }

    public function showAssignModal()
    {
        $this->showAssign = true;
        $this->stopRefresh = true;
    }





    public function dismissModal()
    {
        $this->showCreate = false;
        $this->showEdit = false;
        $this->stopRefresh = false;
        $this->reset();
    }
    
    public function closeModal()
    {
        $this->showCreate = false;
        $this->showEdit = false;
        $this->stopRefresh = false;
    }

    //
    //
    public function updatedPhoto()
    {

        $file = array();

        if ($this->photo != null) {
            $filePath = pathinfo($this->photo->getRealPath());
            $file['name'] = $filePath['filename'];
            $file['extension'] = $filePath['extension'];
            //convert size to MB
            $file['size'] = number_format(filesize($filePath['dirname'] . '/' . $filePath['basename']) * 0.000001, 2);
        }

        $this->photoInfo = $file;
    }

    public function updatedSecondPhoto()
    {

        $file = array();

        if ($this->secondPhoto != null) {
            $filePath = pathinfo($this->secondPhoto->getRealPath());
            $file['name'] = $filePath['filename'];
            $file['extension'] = $filePath['extension'];
            //convert size to MB
            $file['size'] = number_format(filesize($filePath['dirname'] . '/' . $filePath['basename']) * 0.000001, 2);
        }

        $this->secondPhotoInfo = $file;
    }







    //
    public function orderStatus()
    {
        return ['scheduled', 'pending', 'preparing', 'ready', 'enroute', 'failed', 'cancelled', 'delivered'];
    }

    public function orderPaymentStatus()
    {
        return ['pending','request', 'review', 'failed', 'cancelled', 'successful'];
    }



    //
    public function isDemo()
    {
        if (!App::environment('production')) {
            throw new Exception(__("App is in demo version. Some changes can't be made"));
        };
    }





    //FCM
    public $fcmToken;
    public function changeFCMToken($token)
    {
        $this->fcmToken = $token;
        if (Auth::check() && !empty($this->fcmToken)) {
            //
            UserToken::updateOrCreate(
                ['token' => $this->fcmToken],
                ['user_id' => Auth::id()]
            );
        }
    }


    public function logout()
    {
        UserToken::where('token', $this->fcmToken)->delete();
        return redirect()->route('logout');
    }


    public function genColor()
    {
        return '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
    }

}
