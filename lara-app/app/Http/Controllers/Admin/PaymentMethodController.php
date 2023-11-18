<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodController extends Controller
{
    /**
    * @OA\Get(
    *     path="/api/admin/applicant/payment-methods/{applicantId}",
    *     summary="Get linked/not_linked/unlinked payment methods of a specific applicant by an admin",
    *     tags={"PaymentMethods"},
    *     operationId="getPaymentMethodsOfAnApplicantByAdmin",
    *     security={
    *         {"bearerAuth": {}}
    *     },
    *     @OA\Parameter(
    *         name="applicantId",
    *         in="path",
    *         description="ID of the applicant to fetch his/her payment methods",
    *         required=true,
    *         @OA\Schema(type="integer", format="int64")
    *     ),
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
    *             @OA\Property(property="unlinked_payment_methods", type="array", @OA\Items(
    *                 @OA\Property(property="id", type="integer", description="A unique identifier for the linked method in the dataset."),
    *                 @OA\Property(property="payment_method_id", type="integer", description="A unique identifier for the payment method in the dataset."),
    *                 @OA\Property(property="payment_method_name", type="string", description="A descriptive name attribute for the payment method filled by user."),
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
    *         response=404,
    *         description="Applicant not found"
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
    public function getApplicantPaymentMethods($applicant_id)
    {

        $applicant = User::find($applicant_id);
        if(!$applicant || $applicant->role !== UserRoleEnum::Applicant) {
            return response(['message' => 'Applicant not found.'], 404);
        }

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
}
