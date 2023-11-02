<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\LinkPaymentMethodRequest;
use App\Models\LinkedMethod;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/applicant/payment-methods/link",
     *     summary="Link a payment method to the authenticated applicant account",
     *     tags={"PaymentMethods"},
     *     operationId="linkPaymentMethod",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="payment_method_id", type="string", description="Id of the payment method which you intend to link"),
     *             @OA\Property(
     *                 property="payment_method_attributes",
     *                 type="object",
     *                 @OA\Property(property="holder_name", type="string", description="Required for Bank-Transfer linking"),
     *                 @OA\Property(property="bank_name", type="string", description="Required for Bank-Transfer linking"),
     *                 @OA\Property(property="iban", type="string", description="Required for DE Bank-Transfer linking"),
     *                 @OA\Property(property="bic", type="string", description="Required for DE Bank-Transfer linking"),
     *                 @OA\Property(property="account_number", type="string", description="Required for IR Bank-Transfer linking"),
     *                 @OA\Property(property="card_number", type="string", description="Required for IR Bank-Transfer linking"),
     *                 @OA\Property(property="shaba_number", type="string", description="Required for IR Bank-Transfer linking"),
     *                 @OA\Property(property="email", type="string", description="Required for Paypal linking")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
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
    public function linkPaymentMethod(LinkPaymentMethodRequest $request) {

        $applicant = Auth::user();

        $validatedData = $request->validated();
        $payment_method_id = $validatedData['payment_method_id'];

        // Check whther the payment method is linked before or not
        $linked_method = $applicant->linkedMethods()->where('method_type_id',$payment_method_id)->first();
        if($linked_method instanceof LinkedMethod) {
            return response(['message' => 'Payment method is already linked to the applicant account.'], 422);
        }

        // Link the target payment method
        $linked_method = $applicant->linkedMethods()->create([
            "method_type_id" => $payment_method_id
        ]);

        // Attach the payment method's attributes by credential values
        $payment_method = PaymentMethod::find($payment_method_id);
        $input_method_attributes = $validatedData['payment_method_attributes'];
        foreach($input_method_attributes as $input_attr_name => $input_attr_value) {
            $payment_method_attr = $payment_method->attributes()->where('name',$input_attr_name)->first();
            $linked_method->attributes()->attach($payment_method_attr, ['value' => $input_attr_value]);
        }

        return response(['message' => 'Payment method linked successfully'],200);
    }
}
