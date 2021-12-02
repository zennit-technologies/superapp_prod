<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\VendorType;
use Illuminate\Http\Request;




class AppSettingsController extends Controller
{

    public function index(Request $request)
    {
        //
        $currency = Currency::where('country_code', setting("currencyCountryCode", "GH"))->first();

        //vendor types
        $vendorTypes = VendorType::active()->get();

        return response()->json([

            "colors" => setting("appColorTheme"),
            "strings" => [
                "google_maps_key" => setting("googleMapKey", ""),
                "fcm_key" => setting('fcmServerKey', ""),
                "app_name" => setting('appName', ""),
                "company_name" => setting('websiteName', ""),
                "enble_otp" => setting('enableOTP', "1"),
                "enableOTPLogin" => setting('enableOTPLogin', "0"),
                "otpGateway" => setting('otpGateway', ""),
                "enableGoogleDistance" => setting('enableGoogleDistance', "0"),
                "enableSingleVendor" => setting('enableSingleVendor', "1"),
                "enableProofOfDelivery" => setting('enableProofOfDelivery', "1"),
                "orderVerificationType" => setting('orderVerificationType'),
                "enableDriverWallet" => setting('enableDriverWallet', "0"),
                "enableGroceryMode" => setting('enableGroceryMode', "0"),
                "enableReferSystem" => setting('enableReferSystem', "0"),
                "enableChat" => setting('enableChat', "1"),
                "enableOrderTracking" => setting('enableOrderTracking', 1),
                "enableUploadPrescription" => setting('enableUploadPrescription', 1),
                //driver related
                "alertDuration" => setting('alertDuration', 15),
                "driverSearchRadius" => setting('driverSearchRadius', 10),
                "maxDriverOrderAtOnce" => setting('maxDriverOrderAtOnce', 1),
                "distanceCoverLocationUpdate" => setting('distanceCoverLocationUpdate', 10),
                "timePassLocationUpdate" => setting('timePassLocationUpdate', 10),
                //
                "enableParcelVendorByLocation" => setting('enableParcelVendorByLocation', "0"),
                "referRewardAmount" => setting('referRewardAmount', "0"),
                "enableParcelMultipleStops" => setting('enableParcelMultipleStops', "0"),
                "maxParcelStops" => setting('maxParcelStops', "1"),
                "what3wordsApiKey" => setting('what3wordsApiKey', ""),
                "currency" => $currency->symbol,
                "country_code" => setting('appCountryCode', "GH"),
                //links
                "androidDownloadLink" => setting('androidDownloadLink', ""),
                "iosDownloadLink" => setting('iosDownloadLink', ""),
                //
                "isSingleVendorMode" => count($vendorTypes) > 1 ? "0" : "1",
                "enabledVendorType" => $vendorTypes->first(),
                //
                "emergencyContact" => setting('emergencyContact', "911"),

                //auth
                "auth" => [
                    "googleLogin" => (bool) setting("googleLogin"),
                    "appleLogin" => (bool) setting("appleLogin"),
                    "facebbokLogin" => (bool) setting("facebbokLogin"),
                ],
                //taxi
                "taxi" => setting("taxi"),
                //for website 
                "website" => [
                    "websiteHeaderTitle" => setting("websiteHeaderTitle"),
                    "websiteHeaderSubtitle" => setting("websiteHeaderSubtitle"),
                    "websiteHeaderImage" => url(setting("websiteHeaderImage")),
                    "websiteFooterImage" => url(setting("websiteFooterImage")),
                    "websiteIntroImage" => url(setting("websiteIntroImage","")),
                    "websiteFooterBrief" => setting("websiteFooterBrief"),
                    "social" => setting("social"),
                ]
                

            ],

        ]);
    }
}
