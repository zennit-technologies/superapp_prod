<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorType;

class VendorTypeController extends Controller
{


    public function index(Request $request)
    {
        return VendorType::active()->get();
    }
}
