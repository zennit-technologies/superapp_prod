<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Vendor;
use App\Traits\FirebaseAuthTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Propaganistas\LaravelPhone\PhoneNumber;




class AuthController extends Controller
{
    //traits
    use FirebaseAuthTrait;

    //
    public function login(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:users',
                'password' => 'required',
            ],
            $messages = [
                'email.exists' => __('Email not associated with any account'),
            ]
        );

        if ($validator->fails()) {

            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }

        //
        $user = User::where('email', $request->email)->first();

        if (!empty($request->role) && !$user->hasAnyRole($request->role)) {
            return response()->json([
                "message" => __("Unauthorized Access. Please try with an authorized credentials")
            ], 401);
        } else if (!$user->is_active) {
            return response()->json([
                "message" => __("Account is not active. Please contact us")
            ], 401);
        } else if ($request->role == "manager" && empty($user->vendor_id)) {
            return response()->json([
                "message" => __("Manager is not assigned to a vendor. Please assign manager to vendor and try again")
            ], 401);
        } else if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            //generate tokens
            return $this->authObject($user);
        } else {
            return response()->json([
                "message" => __("Invalid credentials. Please check your password and try again")
            ], 401);
        }
    }


    //
    public function verifyPhoneAccount(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'phone' => 'phone:' . setting('countryCode', "GH"),
            ],
            $messages = [
                'phone.exists' => __('Phone not associated with any account'),
            ]
        );

        if ($validator->fails()) {

            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }

        //
        $phone = PhoneNumber::make($request->phone);
        $user = User::where('phone', 'like', '%' . $phone . '')->first();

        if (!empty($user)) {

            //
            $internationalFormat = PhoneNumber::make($phone, setting('countryCode', "GH"))->formatInternational();
            return response()->json([
                "phone" => $internationalFormat
            ], 200);
        } else {
            return response()->json([
                "message" => __("There is no account accoutiated with provided phone number ") . $phone . "",
            ], 400);
        }
    }

    //
    public function passwordReset(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'phone' => 'phone:' . setting('countryCode', "GH"),
            ],
            $messages = [
                'phone.exists' => __('Phone not associated with any account'),
            ]
        );

        if ($validator->fails()) {

            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }

        //
        $phone = PhoneNumber::make($request->phone);
        $user = User::where('phone', 'like', '%' . $phone . '')->first();

        if (empty($user)) {
            return response()->json([
                "message" => __("There is no account accoutiated with provided phone number ") . $phone . "",
            ], 400);
        }

        //verify firebase token
        try {

            //
            $phone = PhoneNumber::make($request->phone);

            if (!empty($request->firebase_id_token)) {
                $firebaseUser = $this->verifyFirebaseIDToken($request->firebase_id_token);

                //verify that the token belongs to the right user
                if ($firebaseUser->phoneNumber == $phone) {

                    //
                    $user = User::where("phone", $phone)->firstorfail();
                    $user->password = Hash::make($request->password);
                    $user->Save();

                    return response()->json([
                        "message" => __("Account Password Updated Successfully"),
                    ], 200);
                } else {
                    return response()->json([
                        "message" => __("Password Reset Failed"),
                    ], 400);
                }
            } else {
                //verify that the token belongs to the right user
                $user = User::where("phone", $phone)->firstorfail();
                $user->password = Hash::make($request->password);
                $user->Save();

                return response()->json([
                    "message" => __("Account Password Updated Successfully"),
                ], 200);
            }
        } catch (\Expection $ex) {
            return response()->json([
                "message" => $ex->getMessage(),
            ], 400);
        }
    }

    //
    public function register(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'phone' => 'phone:' . setting('countryCode', "GH") . '|unique:users',
                'password' => 'required',
            ],
            $messages = [
                'email.unique' => __('Email already associated with an account'),
                'phone.unique' => __('Phone already associated with an account'),
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }


        try {

            //
            $phone = PhoneNumber::make($request->phone);
            // $rawPhone = PhoneNumber::make($request->phone, setting('countryCode', "GH"))->formatNational();
            // $phone = str_replace(' ', '', $rawPhone); 
            // logger("Phone", [$request->phone, $phone]);

            //
            $user = User::where('phone', $phone)->first();
            if (!empty($user)) {
                throw new Exception(__("Account with phone already exists"), 1);
            }


            DB::beginTransaction();
            //
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $phone;
            $user->country_code = $request->country_code ?? "";
            $user->password = Hash::make($request->password);
            $user->is_active = true;
            $user->save();

            //refer system is enabled
            $enableReferSystem = (bool) setting('enableReferSystem', "0");
            $referRewardAmount = (float) setting('referRewardAmount', "0");
            if ($enableReferSystem && !empty($request->code)) {
                //
                $referringUser = User::where('code', $request->code)->first();
                if (!empty($referringUser)) {
                    $referringUser->topupWallet($referRewardAmount);
                } else {
                    throw new Exception(__("Invalid referral code"), 1);
                }
            }

            //
            if (empty($request->role)) {
                $user->syncRoles("client");
            }

            DB::commit();
            //generate tokens
            return $this->authObject($user);
        } catch (Exception $error) {

            DB::rollback();
            return response()->json([
                "message" => $error->getMessage()
            ], 500);
        }
    }

    //
    public function profileUpdate(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'sometimes|string',
                'email' => 'sometimes|email|unique:users,email,' . Auth::id(),
                'phone' => 'phone:' . setting('countryCode', "GH") . '|unique:users,phone,' . Auth::id(),
                'photo' => 'sometimes|nullable|image|max:2048',
            ],
            $messages = [
                'email.unique' => __('Email already associated with an account'),
                'phone.unique' => __('Phone already associated with an account'),
                'photo.max' => __('Photo must be equal or less to 2MB'),
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }


        try {



            DB::beginTransaction();
            //
            $user = User::find(Auth::id());
            //only update the other user data if app is not in demo
            if (!$this->isInDemo()) {
                $user->name = $request->name ?? $user->name;
                $user->email = $request->email ?? $user->email;
                $user->phone = $request->phone ?? $user->phone;
                $user->country_code = $request->country_code ?? $user->country_code;
            }
            $user->is_online = $request->is_online ?? $user->is_online;
            $user->save();

            if (!$this->isInDemo()) {
                if ($request->photo) {
                    $user->clearMediaCollection('profile');
                    $user->addMedia($request->file('photo'))->toMediaCollection('profile');
                }
            }

            DB::commit();
            //generate tokens
            return response()->json([
                "message" => __("User profile updated successful"),
                "user" => $user,
            ]);
        } catch (Exception $error) {

            logger("Profile", [$error]);
            DB::rollback();
            return response()->json([
                "message" => $error->getMessage()
            ], 500);
        }
    }

    //
    public function changePassword(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'password' => 'required',
                'new_password' => 'required|confirmed',
            ],
        );

        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 400);
        }

        //check old password 
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                "message" => __("Invalid Current Password"),
            ], 400);
        }


        try {

            $this->isDemo();
            DB::beginTransaction();
            //
            $user = User::find(Auth::id());
            $user->password = Hash::make($request->new_password);
            $user->save();

            DB::commit();
            //generate tokens
            return response()->json([
                "message" => __("User password updated successful"),
                "user" => $user,
            ]);
        } catch (Exception $error) {

            logger("Profile", [$error]);
            DB::rollback();
            return response()->json([
                "message" => $error->getMessage()
            ], 500);
        }
    }


    //
    public function logout(Request $request)
    {
        $user = User::find(Auth::id());
        if (!empty($user)) {
            if ($user->hasAnyRole('driver')) {
                $user->is_online = 0;
                $user->save();
            }
            Auth::logout();
        }
        return response()->json([
            "message" => "Logout successful"
        ]);
    }

    /**
     *
     * Helpers
     *
     */
    public function authObject($user)
    {

        if (!$user->is_active) {
            throw new Exception(__("User Account is inactive"), 1);
        }
        $user = User::find($user->id);
        $vendor = Vendor::find($user->vendor_id);
        $vehicle = Vehicle::active()->where('driver_id', $user->id)->first();
        $token = $user->createToken($user->name)->plainTextToken;
        return response()->json([
            "token" => $token,
            "fb_token" => $this->fbToken($user),
            "type" => "Bearer",
            "message" => __("User login successful"),
            "user" => $user,
            "vehicle" => $vehicle,
            "vendor" => $vendor,
        ]);
    }

    public function fbToken($user)
    {

        $uId = "user_id_" . $user->id . "";
        $firebaseAuth = $this->getFirebaseAuth();
        $customToken = $firebaseAuth->createCustomToken($uId);
        $customTokenString = $customToken->toString();
        return $customTokenString;
    }
}
