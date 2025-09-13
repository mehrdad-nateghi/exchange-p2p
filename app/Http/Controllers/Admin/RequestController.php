<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Legacy\BidStatusEnum;
use App\Enums\Legacy\RequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Legacy\UpdateRequestRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Requests",
 *     description="APIs for managing Requests"
 * )
 */
class RequestController extends Controller
{

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/requests/remove/{requestId}",
     *     summary="Remove a specific request by an admin",
     *     tags={"Requests"},
     *     operationId="removeRequestByAdmin",
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

        $req = Request::find($requestId);
        if (!$req || $req->status == RequestStatusEnum::Removed ) {
            return response()->json(['message' => 'Request not found.'], 404);
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

    /**
     * @OA\Get(
     *     path="/api/admin/requests/update/setup/{requestId}",
     *     summary="Get setup information for updating a request by admin",
     *     tags={"Requests"},
     *     operationId="getSetupInformationForRequestUpdateByAdmin",
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

        $request = Request::find($requestId);
        if (!$request) {
            return response()->json(['message' => 'Request not found.'], 404);
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
     *     path="/api/admin/requests/update/{requestId}",
     *     summary="Update a specific request by an admin",
     *     tags={"Requests"},
     *     operationId="updateRequestByAdmin",
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
     *         description="Applicant/Request not found or One or more selected payment methods are not available for this applicant",
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

        $req = Request::find($requestId);
        if (!$req || $req->status == RequestStatusEnum::Removed) {
            return response()->json(['message' => 'Request not found.'], 404);
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

        DB::beginTransaction();

        try {
            $req->update($updateData);

            // Handle payment methods
            $requester = $req->user;
            $requester_payment_methods = $this->userRepository->getPaymentMethodsUserLinked($requester);

            $request_payment_methods = $validated_data['request_payment_methods'];

            // Check if the request payment methods exist and are associated with the requester
            $difference = array_diff($request_payment_methods, $requester_payment_methods);
            if (!empty($difference)) {
                throw new \Exception('One or more selected payment methods are not available for the applicant who initiated the request.');
            }

            // Retrieve peer linked methods based on the request payment methods
            $request_linked_methods = $this->userRepository->getPeerLinkedMethods($requester, $request_payment_methods);

            // Sync the payment methods for the request
            $req->linkedMethods()->sync($request_linked_methods);

            DB::commit();

            return response(['message' => 'Request updated successfully'],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => $e->getMessage()], 500);
        }
    }
}
