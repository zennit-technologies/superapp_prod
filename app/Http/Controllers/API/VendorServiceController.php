<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorServiceController extends Controller
{

    public function index()
    {
        return Service::where('vendor_id', Auth::user()->vendor_id)->paginate();
    }

    public function store(Request $request)
    {

        $user = User::find(auth('api')->id());
        //
        if (!$user->hasAnyRole('manager') || $user->vendor_id != $request->vendor_id) {
            return response()->json([
                "message" => __("You are not allowed to perform this operation")
            ], 400);
        }

        try {

            DB::beginTransaction();
            $service = Service::create($request->all());
            if ($request->hasFile("photos")) {
                $service->clearMediaCollection();
                foreach ($request->file('photos') as $photo) {
                    $service->addMedia($photo->getRealPath())->toMediaCollection();
                }
            }
            DB::commit();

            return response()->json([
                "message" => __("Service created successfully"),
                "service" => $service,
            ]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                "message" => $ex->getMessage()
            ], 400);
        }
    }


    public function update(Request $request, $id)
    {

        $user = User::find(auth('api')->id());
        //
        if (!$user->hasAnyRole('manager') || $user->vendor_id != $request->vendor_id) {
            return response()->json([
                "message" => __("You are not allowed to perform this operation")
            ], 400);
        }

        try {

            DB::beginTransaction();
            $service = Service::find($id);
            $service->update($request->all());
            $service->refresh();
            if ($request->hasFile("photos")) {
                $service->clearMediaCollection();
                foreach ($request->file('photos') as $photo) {
                    $service->addMedia($photo->getRealPath())->toMediaCollection();
                }
            }
            DB::commit();
            return response()->json([
                "message" => __("Service updated successfully"),
                "service" => $service,
            ]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                "message" => $ex->getMessage()
            ], 400);
        }
    }


    public function destroy($id)
    {

        $service = Service::find($id);
        $user = User::find(auth('api')->id());
        //
        if (!$user->hasAnyRole('manager') || $user->vendor_id != $service->vendor_id) {
            return response()->json([
                "message" => __("You are not allowed to perform this operation")
            ], 400);
        }

        try {

            DB::beginTransaction();
            Service::destroy($id);
            DB::commit();

            return response()->json([
                "message" => __("Service deleted successfully"),
            ]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                "message" => $ex->getMessage()
            ], 400);
        }
    }
}
