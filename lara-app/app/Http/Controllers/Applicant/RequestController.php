<?php

namespace App\Http\Controllers\Applicant;

use App\Enums\BidStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\RequestResource;
use App\Models\Request as RequestModel;
use App\Http\Requests\CreateRequestRequest;
use App\Http\Requests\UpdateRequestRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\Country;
use App\Models\Financial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Requests",
 *     description="APIs for managing Requests"
 * )
 */
class RequestController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/applicant/requests",
     *     summary="Get all requests of an authenticated applicant",
     *     tags={"Requests"},
     *     operationId="getAllRequestsOfAnAuthenticatedApplicant",
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
    public function getOwnAllRequests()
    {
        $user = Auth::user();
        $requests = $user->requests()->get();

        return response(['requests' => RequestResource::collection($requests)], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/applicant/requests/{requestId}",
     *     summary="Get specific request of an authenticated applicant",
     *     tags={"Requests"},
     *     operationId="getSpecificRequestOfAnAuthenticatedApplicant",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="requestId",
     *         in="path",
     *         description="ID of the request",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Request not found"
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
     *     )
     * )
     */
    public function getOwnRequest($requestId)
    {
        $user = Auth::user();
        $request = $user->requests()->where('id',$requestId)->first();
        if($request instanceof RequestModel) {
            return response(['request' => new RequestResource($request)], 200);
        }
        else {
            return response(['message' => 'Request not found.'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/applicant/requests/create/setup/{countryId}",
     *     summary="Get setup information for request creation",
     *     tags={"Requests"},
     *     operationId="getSetupInformationForRequestCreation",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="countryId",
     *         in="path",
     *         description="ID of the country to fetch its paymentMethods",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Country/FinancialInformation/EuroDailyRate not found"
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
     *     )
     * )
     */
    public function getSetupInformationForRequestCreation($countryId){

        $country = Country::find($countryId);
        if(!($country instanceof Country)) {
            return response()->json(['message' => 'Country not found!'], 404);
        }

        $system_payment_methods = $country->paymentMethods()->get();

        $euro_daily_rate = config('constants.Euro_Daily_Rate');

        // get feasibility range
        $feasibility_range_response = $this->getFeasibilityRange();
        if($feasibility_range_response['status'] == 200){
            $result = [
                'payment_methods' => PaymentMethodResource::collection($system_payment_methods),
                'feasibility_range' => $feasibility_range_response['feasibility_range'],
                'euoro_daily_rate' => $euro_daily_rate
            ];
            return response()->json(['data' => $result], 200);
        } else{
            return response()->json(['message' => $feasibility_range_response['message']], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/applicant/requests/create",
     *     summary="Create new request",
     *     tags={"Requests"},
     *     operationId="createRequest",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="applicant_id", type="number"),
     *             @OA\Property(property="type", type="number", enum={0, 1}, description="0: Buy Request, 1: Sell Request"),
     *             @OA\Property(property="trade_volume", type="number"),
     *             @OA\Property(property="lower_bound_feasibility_threshold", type="number"),
     *             @OA\Property(property="upper_bound_feasibility_threshold", type="number"),
     *             @OA\Property(property="acceptance_threshold", type="number"),
     *             @OA\Property(property="request_rate", type="number"),
     *             @OA\Property(property="request_payment_methods", type="array", @OA\Items(type="integer")),
     *             @OA\Property(property="description", type="string"),
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
     *         response=404,
     *         description="Applicant not found or One or more selected payment methods are not available for the applicant",
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
    public function create(CreateRequestRequest $request){

        $applicant = Auth::user();
        $validated_data = $request->validated();

        // Check if the request payment methods exist and associated with applicant
        $applicant_linked_methods = $applicant->linkedMethods;
        $request_payment_methods = $validated_data['request_payment_methods'];
        $applicant_payment_methods = [];
        foreach($applicant_linked_methods as $lm){
            $payment_method = $lm->paymentMethod;
            array_push($applicant_payment_methods, $payment_method->id);
        }
        $difference = array_diff($request_payment_methods, $applicant_payment_methods); // Check all items of request_payment_methods list exist in $applicant_payment_methods
        if(!empty($difference)) {
            return response()->json(['message' => 'One or more selected payment methods are not available for this applicant.'], 404);
        }

        // Create request on database
        $new_request = RequestModel::create([
            'type' => $validated_data['type'],
            'support_id' => Str::uuid(),
            'trade_volume' => $validated_data['trade_volume'],
            'lower_bound_feasibility_threshold' => $validated_data['lower_bound_feasibility_threshold'],
            'upper_bound_feasibility_threshold' => $validated_data['upper_bound_feasibility_threshold'],
            'acceptance_threshold' => $validated_data['acceptance_threshold'],
            'request_rate' => $validated_data['request_rate'],
            'description' => $validated_data['description'],
            'status' => \App\Enums\RequestStatusEnum::Pending ,
            'is_removed' => False,
            'applicant_id' => $applicant->id
        ]);

        if($new_request instanceof RequestModel) {
            // Set the support_id using the pattern 'RE' + id
            $new_request->update(['support_id' => config('constants.SupportId_Prefixes.Request_Pr') . $new_request->id]);

            // Attach payment methods to the request using the relationship
            $new_request->paymentMethods()->attach($request_payment_methods);

            // Refresh the object to ensure attributes like created_at are up-to-date
            $new_request->refresh();

            return response()->json(['message' => 'Request created successfully.', 'request' => new RequestResource($new_request)], 200);
        }
        else {
            return response()->json(['message' => 'An error occurred while creating the request.'], 500);
        }
    }

     /**
     * @OA\Get(
     *     path="/api/applicant/requests/update/setup/{requestId}",
     *     summary="Get setup information for updating a request by applicant who initiated the request",
     *     tags={"Requests"},
     *     operationId="getSetupInformationForRequestUpdateByApplicant",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="requestId",
     *         in="path",
     *         description="ID of the request to fetch it for editing purpose",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Applicant/Request not found"
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
     *     )
     * )
     */
    public function getSetupInformationForRequestUpdate($requestId){

        $applicant = Auth::user();

        $request = $applicant->requests()->where('id', $requestId)->first();
        if (!$request) {
            return response()->json(['message' => 'Request not found for the applicant.'], 404);
        }

        $euro_daily_rate = config('constants.Euro_Daily_Rate');

        // get feasibility range
        $feasibility_range_response = $this->getFeasibilityRange();
        if($feasibility_range_response['status'] == 404){
            return response()->json(['message' => $feasibility_range_response['message']], 404);
        }

        $result = [
            'feasibility_range' => $feasibility_range_response['feasibility_range'],
            'euoro_daily_rate' => $euro_daily_rate,
            'request' => [
                'id' => $request['id'],
                'support_id' => $request['support_id'],
                'trade_volume' => $request['trade_volume'],
                'request_rate' => $request['request_rate'],
                'description' => $request['description'],
                'payment_methods' => PaymentMethodResource::collection($request->getRequestPaymentMethods())
            ]
        ];

        return response()->json(['data' => $result], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/applicant/requests/update/{requestId}",
     *     summary="Update a specific request by an applicant who initiated the request",
     *     tags={"Requests"},
     *     operationId="updateRequestByApplicant",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="requestId",
     *         in="path",
     *         description="ID of the request to fetch it for updating purpose",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="trade_volume", type="number"),
     *             @OA\Property(property="lower_bound_feasibility_threshold", type="number"),
     *             @OA\Property(property="upper_bound_feasibility_threshold", type="number"),
     *             @OA\Property(property="request_rate", type="number"),
     *             @OA\Property(property="request_payment_methods", type="array", @OA\Items(type="integer")),
     *             @OA\Property(property="description", type="string", description="There are three different conditions for this field: 1. When you want to update the value of description field, you have to pass the description by desired value. 2. When you don't want to update the value of description field, you have not to pass the description field by your request. 3. When you want to update the value of description field to the NULL, you have to pass the description by empty value like this: description:"" ."),
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
     *         response=404,
     *         description="Request not found or One or more selected payment methods are not available for this applicant",
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
    public function update(UpdateRequestRequest $request, $requestId){

        $applicant = Auth::user();

        $req = $applicant->requests()->where('id', $requestId)->first();
        if (!$req || $req->status == RequestStatusEnum::Removed) {
            return response()->json(['message' => 'Request not found for the applicant.'], 404);
        }

        // Check whether the request has no associated bids
        if(!($req->bids->isEmpty())) {
            return response()->json(['message' => 'The request has one or more associated bids.'], 422); // 422 Unprocessable Request
        }

        $validated_data = $request->validated();

        $updateData = [
            'trade_volume' => $validated_data['trade_volume'],
            'request_rate' => $validated_data['request_rate'],
            'acceptance_threshold' => $validated_data['request_rate'],
            'lower_bound_feasibility_threshold' => $validated_data['lower_bound_feasibility_threshold'],
            'upper_bound_feasibility_threshold' => $validated_data['upper_bound_feasibility_threshold'],
        ];

        if (array_key_exists('description', $validated_data)) {
            $updateData['description'] = $validated_data['description'];
        }

        $req->update($updateData);

        // Handle payment methods
        $applicant_linked_methods = $applicant->linkedMethods;

        $applicant_payment_methods = $applicant_linked_methods->map(function ($linkedMethod) {
            return $linkedMethod->paymentMethod->id;
        })->toArray();

        $request_payment_methods = $validated_data['request_payment_methods'];

        // Check if the request payment methods exist and are associated with the applicant
        $difference = array_diff($request_payment_methods, $applicant_payment_methods);
        if (!empty($difference)) {
            return response()->json(['message' => 'One or more selected payment methods are not available for the applicant.'], 404);
        }

        // Sync the payment methods for the request
        $req->paymentMethods()->sync($validated_data['request_payment_methods']);

        return response()->json(['message' => 'Request updated successfully'],200);
    }

    /**
     * @OA\Delete(
     *     path="/api/applicant/requests/remove/{requestId}",
     *     summary="Remove a specific request by an applicant who initiated the request",
     *     tags={"Requests"},
     *     operationId="removeRequestByApplicant",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="requestId",
     *         in="path",
     *         description="ID of the request to remove",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Request not found",
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
    public function remove($requestId){

        $applicant = Auth::user();

        $req = $applicant->requests()->where('id', $requestId)->first();
        if (!$req || $req->status == RequestStatusEnum::Removed) {
            return response()->json(['message' => 'Request not found for the applicant.'], 404);
        }

        $bids = $req->bids;
        $bids->each(function ($bid) {
            $bid->status = BidStatusEnum::Invalid;
            $bid->save();
        });

        $req->status = RequestStatusEnum::Removed;
        $req->save();

        return response()->json(['message' => 'Request removed successfully'],200);
    }

}
