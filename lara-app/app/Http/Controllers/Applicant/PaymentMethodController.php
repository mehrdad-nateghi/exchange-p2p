<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use App\Enums\BidStatusEnum;
use App\Enums\LinkedMethodStatusEnum;
use App\Enums\RequestStatusEnum;
use Illuminate\Support\Facades\Log;

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
     *     summary="Get linked/not_linked/unlinked payment methods of the authenticated applicant",
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
        $applicant_linked_methods = $applicant->getLinkedPaymentMethods();
        $reformatted_linked_payment_methods = $applicant_linked_methods->map(function ($lm) {
            return $lm->formatAttributes();
        });
        $response['linked_payment_methods'] = $reformatted_linked_payment_methods;

        // Fetch unlinked payment methods of the applicant
        $applicant_ulinked_methods = $applicant->getUnlinkedPaymentMethods();
        $reformatted_ulinked_payment_methods = $applicant_ulinked_methods->map(function ($lm) {
            return $lm->formatAttributes();
        });
        $response['ulinked_payment_methods'] = $reformatted_ulinked_payment_methods;

        // Fetch not linked payment methods of the applicant
        $system_payment_methods = PaymentMethod::all();
        $linkedMethodIds = $reformatted_linked_payment_methods->pluck('payment_method_id')->toArray();
        $reformatted_not_linked_payment_methods = $system_payment_methods->reject(function ($pm) use ($linkedMethodIds) {
            return in_array($pm['id'], $linkedMethodIds);
        })->values()->all();
        $response['not_linked_payment_methods'] = $reformatted_not_linked_payment_methods;

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
