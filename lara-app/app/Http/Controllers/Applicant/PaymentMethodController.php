<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;


class PaymentMethodController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/applicant/payment-methods/get",
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
        $applicant_linked_methods = $applicant->linkedMethods;
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

}
