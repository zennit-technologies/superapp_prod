<?php

namespace App\Http\Livewire\Auth;

use App\Http\Livewire\BaseLivewireComponent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordLivewire extends BaseLivewireComponent
{

    public $phone;
    public $setPassword = false;
    public $otp;
    public $idToken;
    public $password;
    public $password_confirmation;
    protected $listeners = ['allowReset' => 'showResetForm'];


    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('layouts.auth');
    }


    public function initiateFireabse(){
        $this->emit('initiateFirebaseAuth', setting('apiKey', "") );
    }

    public function resetPassword(){

        $this->validate(
            [
                "phone" => "phone:AUTO,US|exists:users"
            ],
            [
                "phone.exists" => __("No account associated with phone")
            ]
        );
        $this->emit('sendOTP', $this->phone);

    }

    //
    public function verifyOTP(){

        $this->validate([
            "otp" => "required|size:6"
        ]);
        $this->emit('verifyFirebaseAuth', $this->otp );

    }


    public function showResetForm( $idToken ){
        $this->idToken = $idToken;
        $this->setPassword = true;
    }

    public function saveNewPassword(){

        $this->validate([
            "password" => 'required|min:6',
            "password_confirmation" => 'required|same:password|min:6',
        ]);

        //
        if( !empty($this->idToken) ){
            $user = User::where('phone', $this->phone)->first();
            $user->password = Hash::make($this->password);
            $user->Save();

            //
            $this->phone = "";
            $this->setPassword = false;
            $this->showSuccessAlert(__("Account password updated. You can now login with the newly created account password"));

        }

    }


}
