<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use App\Enums\BidStatusEnum;
use App\Enums\LinkedMethodStatusEnum;
use App\Enums\RequestStatusEnum;


/**
 * @OA\Tag(
 *     name="PaymentMethods",
 *     description="APIs for managing Payment Methods"
 * )
 */
class PaymentMethodController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/applicant/payment-methods",
     *     summary="Get linked/unlinked payment methods of the authenticated applicant",
     *     tags={"PaymentMethods"},
     *     operationId="getPaymentMethods",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     *     )
     * )
     */
    public function getPaymentMethods()
    {
        $applicant = Auth::user();
        $response = [];

        // Fetch linked payment methods of the applicant
        $applicant_linked_methods = $applicant->linkedMethods()->where('status', LinkedMethodStatusEnum::Active)->get();
        $reformatted_linked_payment_methods = $applicant_linked_methods->map(function ($lm) {
            $lm_attributes = $lm->attributes->map(function ($attr) {
                return [
                    'attribute_id' => $attr['id'],
                    'attribute_name' => $attr['name'],
                    'value' => $attr['pivot']['value']
                ];
            });

            return [
                'payment_method_id' => $lm['method_type_id'],
                'payment_method_name' => $lm->paymentMethod->name,
                'country_id' => $lm->paymentMethod->country->id,
                'payment_method_attributes' => $lm_attributes
            ];
        });

        $response['linked_payment_methods'] = $reformatted_linked_payment_methods;

        // Fetch unlinked payment methods of the applicant
        $reformatted_unlinked_payment_methods = [];
        $system_payment_methods = PaymentMethod::all();

        foreach ($system_payment_methods as $pm) {
            $islinked = false;
            foreach ($reformatted_linked_payment_methods as $lm) {
                if ($pm['id'] == $lm['payment_method_id']) {
                    $islinked = true;
                    break;
                }
            }

            if (!$islinked) {
                $reformatted_unlinked_payment_methods[] = $pm;
            }
        }

        $response['unlinked_payment_methods'] = $reformatted_unlinked_payment_methods;

        return response()->json($response, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/applicant/payment-methods/unlink/{linkedMethodId}",
     *     summary="Unlink a specific linked method by an applicant who linked the method",
     *     tags={"PaymentMethods"},
     *     operationId="unlinkPaymentMethodByApplicant",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="linkedMethodId",
     *         in="path",
     *         description="ID of the linked method to unlink",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", description="A descriptive attribute indicating the result of request.")
     *      )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Linked method not found",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function unlinkPaymentMethod($linked_method_id){

        $applicant = Auth::user();

        // Check whether the linked method is available for the applicant
        $linked_method = $applicant->linkedMethods()->where('id',$linked_method_id)->first();
        if(!$linked_method || $linked_method->status == LinkedMethodStatusEnum::Removed){
            return response(['message' => 'Linked method not found for the applicant.'], 404);
        }

        // Check whether the linked method is associated with an active request or bid
        $valid_to_unlink = true;

        $requests_linked_methods_id = $applicant->requests()
        ->where('status', '!=', RequestStatusEnum::Removed)
        ->with('linkedMethods:id')
        ->get()
        ->pluck('linkedMethods.*.id')
        ->flatten()
        ->all();

        if (in_array($linked_method_id, $requests_linked_methods_id)) {
            $valid_to_unlink = false;
        }

        $associatedBids = $linked_method->bids()
                ->whereNotIn('status', [BidStatusEnum::Rejected, BidStatusEnum::Invalid])
                ->get();

        if (!$associatedBids->isEmpty()) {
            $valid_to_unlink = false;
        }

        if(!$valid_to_unlink) {
            return response(['message' => 'The linked method is already associated with an active request or bid.'], 403);
        }

        $linked_method->status = LinkedMethodStatusEnum::Removed;
        $linked_method->save();

        return response(['message' => 'Payment method unlinked successfully'],200);
    }
}
