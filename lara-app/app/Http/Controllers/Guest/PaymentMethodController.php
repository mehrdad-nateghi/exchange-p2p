<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{

    /**
    * @OA\Get(
    *     path="/api/payment-methods",
    *     summary="Get all the system payment methods",
    *     tags={"PaymentMethods"},
    *     operationId="getSystemPaymentMethods",
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="payment_methods", type="array", @OA\Items(
    *                 @OA\Property(property="id", type="integer", description="A unique identifier for the payment method in the dataset."),
    *                 @OA\Property(property="name", type="string", description="A descriptive name attribute for the payment method filled by the user."),
    *                 @OA\Property(property="country_id", type="integer", description="A unique identifier for the country which the payment method is associated with."),
    *             ))),
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Internal Server Error",
    *     )
    * )
    */
    public function getPaymentMethods(){

        $paymentMethods = PaymentMethod::with('country')->get();

        $result = [];
        foreach ($paymentMethods as $paymentMethod) {
            $result[] = [
                'id' => $paymentMethod->id,
                'name' => $paymentMethod->name,
                'country_id' => $paymentMethod->country->id
            ];
        }

        return response(["payment_methods" => $result], 200);
    }
}
