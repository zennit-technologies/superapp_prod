<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Service;
use App\Models\Vendor;

class SearchController extends Controller
{
    //
    public function index(Request $request)
    {

        $products = [];
        $vendors = [];
        $services = [];

        //
        if (empty($request->type) || $request->type == "product") {

            $products = Product::active()->when($request->type == "best", function ($query) {
                return $query->withCount('sales')->orderBy('sales_count', 'DESC');
            })
                ->when($request->type == "you", function ($query) {

                    if (auth('sanctum')->user()) {
                        return $query->whereHas('purchases')->withCount('purchases')->orderBy('purchases_count', 'DESC');
                    } else {
                        return $query->inRandomOrder();
                    }
                })
                ->when($request->keyword, function ($query) use ($request) {
                    return $query->where('name', "like", "%" . $request->keyword . "%");
                })
                ->when($request->category_id, function ($query) use ($request) {
                    return $query->whereHas("categories", function ($query) use ($request) {
                        return $query->where('category_id', "=", $request->category_id);
                    });
                })
                ->when($request->vendor_id, function ($query) use ($request) {
                    return $query->where('vendor_id', $request->vendor_id);
                })
                ->when($request->vendor_type_id, function ($query) use ($request) {
                    return $query->whereHas("vendor", function ($query) use ($request) {
                        return $query->where('vendor_type_id', "=", $request->vendor_type_id);
                    });
                })
                ->paginate();
        } else if ($request->type == "service") {

            $services = Service::active()->when($request->type == "best", function ($query) {
                return $query->withCount('sales')->orderBy('sales_count', 'DESC');
            })
                ->when($request->keyword, function ($query) use ($request) {
                    return $query->where('name', "like", "%" . $request->keyword . "%");
                })
                ->when($request->category_id, function ($query) use ($request) {
                    return $query->where('category_id', "=", $request->category_id);
                })
                ->when($request->is_open, function ($query) use ($request) {
                    return $query->where('is_open', "=", $request->is_open);
                })
                ->when($request->vendor_type_id, function ($query) use ($request) {
                    return $query->whereHas('vendor', function ($query) use ($request) {
                        return $query->active()->where('vendor_type_id', $request->vendor_type_id);
                    });
                })
                ->when($request->vendor_id, function ($query) use ($request) {
                    return $query->where('vendor_id', $request->vendor_id);
                })
                ->paginate();;
        } else {
            //
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $vendors = Vendor::active()
                ->when($request->keyword, function ($query) use ($request) {
                    return $query->where('name', "like", "%" . $request->keyword . "%");
                })
                ->when($request->is_open, function ($query) use ($request) {
                    return $query->where('is_open', "=", $request->is_open);
                })
                ->when($request->category_id, function ($query) use ($request) {
                    return $query->whereHas("categories", function ($query) use ($request) {
                        return $query->where('category_id', "=", $request->category_id);
                    });
                })
                ->when($request->vendor_type_id, function ($query) use ($request) {
                    return $query->where('vendor_type_id', "=", $request->vendor_type_id);                    
                })
                ->when($latitude, function ($query) use ($latitude, $longitude) {
                    return $query->distance($latitude, $longitude)
                        ->havingRaw("delivery_range >= distance");
                })->paginate();
        }
        return response()->json([
            "products" => $products,
            "vendors" => $vendors,
            "services" => $services,
        ], 200);
    }
}
