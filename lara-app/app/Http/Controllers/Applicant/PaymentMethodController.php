<?php

namespace App\Http\Controllers\Applicant;

use App\Enums\LinkedMethodStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UnlinkPaymentMethodRequest;
use App\Models\LinkedMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    // public function unlinkPaymentMethod($linked_payment_method_id){

    //     $applicant = Auth::user();

    //     // Check whether the linked method is available for the applicant
    //     $linked_method = $applicant->linkedMethods()->where('id',$linked_payment_method_id)->first();
    //     if(!$linked_method || $linked_method->status == LinkedMethodStatusEnum::Removed){
    //         return response(['message' => 'Linked payment method not found for the applicant.'], 404);
    //     }

    //     // Check whether the linked method is associated with an active request or bid
    //     $valid_to_unlink = true;

    //     $requests_payment_method_ids = $applicant->requests()
    //     ->where('status', '!=', RequestStatusEnum::Removed)
    //     ->with('paymentMethods:id')
    //     ->get()
    //     ->pluck('paymentMethods.*.id')
    //     ->flatten()
    //     ->all();
    //     if(in_array($linked_payment_method_id, $requests_payment_method_ids)) {
    //         $valid_to_unlink = false;
    //     }
    //     else {
    //         if ($linked_method) {
    //             $associatedBids = $linkedMethod->bids()
    //                 ->whereNotIn('status', ['rejected', 'invalid'])
    //                 ->get();

    //     }


    //     Log::info(json_encode($linked_payment_method_id));
    // }
//}
}
