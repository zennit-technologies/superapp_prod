<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;


class CategoryController extends Controller
{

    //
    public function index(Request $request)
    {

        // vendor_type_id
        if ($request->type == "sub") {
            return CategoryResource::collection(
                Subcategory::active()->when($request->vendor_type_id, function ($query) use ($request) {
                    return $query->whereHas('category', function ($query) use ($request) {
                        return $query->where('vendor_type_id', $request->vendor_type_id);
                    });
                })->get()
            );
        }
        return CategoryResource::collection(
            Category::active()->when($request->vendor_type_id, function ($query) use ($request) {
                return $query->where('vendor_type_id', $request->vendor_type_id);
            })->get()
        );
    }
}
