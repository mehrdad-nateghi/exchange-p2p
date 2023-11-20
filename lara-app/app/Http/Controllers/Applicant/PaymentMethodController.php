<?php

namespace App\Http\Controllers\Applicant;

use App\Enums\LinkedMethodStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LinkPaymentMethodRequest;
use App\Models\LinkedMethod;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    *     summary="Get linked/not_linked payment methods of the authenticated applicant",
    *     tags={"PaymentMethods"},
    *     operationId="getPaymentMethods",
    *     security={
    *         {"bearerAuth": {}}
    *     },
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="linked_payment_methods", type="array", @OA\Items(
    *                 @OA\Property(property="id", type="integer", description="A unique identifier for the linked method in the dataset."),
    *                 @OA\Property(property="payment_method_id", type="integer", description="A unique identifier for the payment method in the dataset."),
    *                 @OA\Property(property="payment_method_name", type="string", description="A descriptive name attribute for the payment method filled by the user."),
    *                 @OA\Property(property="country_id", type="integer", description="A unique identifier for the country which the payment method is associated with."),
    *                 @OA\Property(property="payment_method_attributes", type="array", @OA\Items(
    *                     @OA\Property(property="attribute_id", type="integer", description="A unique identifier for the attribute in the dataset."),
    *                     @OA\Property(property="attribute_name", type="string", description="A descriptive attribute for presenting the payment method attribute's name."),
    *                     @OA\Property(property="value", type="string", description="An attribute for presenting the value of payment method attribute filled by the user."),
    *                 )),
    *             )),
    *             @OA\Property(property="not_linked_payment_methods", type="array", @OA\Items(
    *                 @OA\Property(property="id", type="integer", description="A unique identifier or reference for a particular record in the dataset."),
    *                 @OA\Property(property="name", type="string", description="A descriptive attribute for presenting the payment method name."),
    *                 @OA\Property(property="country_id", type="integer", description="A unique identifier or reference for a particular country which the payment method is associated with."),
    *             )),
    *         ),
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
     * @OA\Post(
     *     path="/api/applicant/payment-methods/link/{paymentMethodId}",
     *     summary="Link a payment method to the authenticated applicant account",
     *     tags={"PaymentMethods"},
     *     operationId="linkPaymentMethod",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="paymentMethodId",
     *         in="path",
     *         description="Id of the payment method which you intend to link",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="payment_method_attributes",
     *                 type="object",
     *                 @OA\Property(property="holder_name", type="string", description="Required for Bank-Transfer linking"),
     *                 @OA\Property(property="bank_name", type="string", description="Required for Bank-Transfer linking"),
     *                 @OA\Property(property="iban", type="string", description="Required for DE Bank-Transfer linking"),
     *                 @OA\Property(property="bic", type="string", description="Required for DE Bank-Transfer linking"),
     *                 @OA\Property(property="account_number", type="string", description="Optional for IR Bank-Transfer linking"),
     *                 @OA\Property(property="card_number", type="string", description="Required for IR Bank-Transfer linking"),
     *                 @OA\Property(property="shaba_number", type="string", description="Required for IR Bank-Transfer linking"),
     *                 @OA\Property(property="email", type="string", description="Required for Paypal linking")
     *             )
     *         )
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
     *         description="Payment Method not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable request",
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
    public function linkPaymentMethod(LinkPaymentMethodRequest $request, $payment_method_id) {

        $applicant = Auth::user();

        $payment_method = PaymentMethod::find($payment_method_id);
        if(!($payment_method instanceof PaymentMethod)){
            return response(['message' => 'Payment method not found!'],404);
        }

        $validatedData = $request->validated();

        // Check whther the payment method is linked before or not
        $linked_method = $applicant->getLinkedMethodByPaymentMethodIdIfIsActive($payment_method_id);
        if($linked_method instanceof LinkedMethod) {
            return response(['message' => 'Payment method is already linked to the applicant account.'], 422);
        }

        DB::beginTransaction();

        // Link the target payment method
        $linked_method = $applicant->linkedMethods()->create([
            "method_type_id" => $payment_method_id
        ]);

        // Attach the payment method's attributes by credential values
        $payment_method = PaymentMethod::find($payment_method_id);
        $input_method_attributes = $validatedData['payment_method_attributes'];
        $initiation = $linked_method->initiateAttributes($payment_method, $input_method_attributes);
        if (!$initiation) {
            // If attributes initiation fails, rollback the transaction
            DB::rollBack();
            return response(['message' => 'Some of the attributes are not available in the database.'], 422);
        }

        DB::commit();

        return response(['message' => 'Payment method linked successfully'],200);
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
        $linked_method = $applicant->getLinkedMethodIfIsActive($linked_method_id);
        if(!$linked_method){
            return response(['message' => 'Linked payment method not found for the applicant.'], 404);
        }

        // Check whether the linked method is associated with an active request or bid
        if($linked_method->isEngagedWithAnyActiveRequest() || $linked_method->isEngagedWithAnyActiveBid()) {
            return response(['message' => 'The linked method is already associated with an active request or bid.'], 403);
        }

        $linked_method->status = LinkedMethodStatusEnum::Removed;
        $linked_method->save();

        return response(['message' => 'Payment method unlinked successfully'],200);
    }

    /**
     * @OA\Put(
     *     path="/api/applicant/payment-methods/linked-method/update/{linkedMethodId}",
     *     summary="Update a specific linked method by an applicant who linked the method",
     *     tags={"PaymentMethods"},
     *     operationId="updateLinkedPaymentMethodByApplicant",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="linkedMethodId",
     *         in="path",
     *         description="ID of the linked method to update",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="payment_method_attributes",
     *                 type="object",
     *                 @OA\Property(property="holder_name", type="string", description="Required for Bank-Transfer linking"),
     *                 @OA\Property(property="bank_name", type="string", description="Required for Bank-Transfer linking"),
     *                 @OA\Property(property="iban", type="string", description="Required for DE Bank-Transfer linking"),
     *                 @OA\Property(property="bic", type="string", description="Required for DE Bank-Transfer linking"),
     *                 @OA\Property(property="account_number", type="string", description="Optional for IR Bank-Transfer linking"),
     *                 @OA\Property(property="card_number", type="string", description="Required for IR Bank-Transfer linking"),
     *                 @OA\Property(property="shaba_number", type="string", description="Required for IR Bank-Transfer linking"),
     *                 @OA\Property(property="email", type="string", description="Required for Paypal linking")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", description="A descriptive attribute indicating the result of request.")
     *      )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable request",
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
    public function updateLinkedMethod(LinkPaymentMethodRequest $request, $linked_method_id){

        $applicant = Auth::user();

        $validated_credentials = $request->validated();

        $linked_method = $applicant->getLinkedMethodIfIsActive($linked_method_id);
        if(!$linked_method){
            return response(['message' => 'Linked payment method not found for the applicant.'], 404);
        }

        if($linked_method->isEngagedWithAnyActiveRequest() || $linked_method->isEngagedWithAnyActiveBid()) {
            return response(['message' => 'The linked method is already associated with an active request or bid.'], 403);
        }

        // Update the payment method's attributes by credential values
        $input_method_attributes = $validated_credentials['payment_method_attributes'];
        $update_attrs = $linked_method->updateAttributes($input_method_attributes);
        if(!$update_attrs) {
            return response(['message' => 'Some problems occured during the updating associated fields.'], 500);
        }

        return response(['message' => 'Linked payment method updated successfully'],200);
    }
}
