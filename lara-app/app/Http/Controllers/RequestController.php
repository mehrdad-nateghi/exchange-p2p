<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequestResource;
use App\Models\Request as RequestModel;
use App\Models\User as UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Requests",
 *     description="APIs for managing requests"
 * )
 */
class RequestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/requests",
     *     summary="Get all sell requests",
     *     tags={"Requests"},
     *      @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter requests by type (sell or buy)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"sell", "buy"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        if ($request->has('type')){
            $type = $request->input('type') == 'sell'? \App\Enums\RequestTypeEnum::Sell : \App\Enums\RequestTypeEnum::Buy;
            $requests = RequestModel::where('type' , $type)->get();
        }
        else{
            $requests = RequestModel::all();
        }

        return response()->json(RequestResource::collection($requests), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/requests/applicant/{applicantId}",
     *     summary="Get all requests of the specific applicant",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="applicantId",
     *         in="path",
     *         description="ID of the applicant to fetch its requests",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Applicant not found"
     *     )
     *     )
     * )
     */
    public function getApplicantAllRequests($applicantId)
    {
        $user = UserModel::find($applicantId);

        $response = '';

        if($user instanceof UserModel && $user->type == \App\Enums\UserTypeEnum::Applicant) {
            $requests = $user->requests()->get();
            $response = response()->json(['requests' => RequestResource::collection($requests)], 200);
        }
        else {
            $response = response()->json(['message' => 'Applicant not found.'], 404);
        }

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/requests/applicant/{applicantId}/{requestId}",
     *     summary="Get specific request of the specific applicant",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="applicantId",
     *         in="path",
     *         description="ID of the applicant to fetch its request",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
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
     *         description="Applicant not found or Request not found"
     *     )
     *     )
     * )
     */
    public function getApplicantRequest($applicantId, $requestId)
    {
        $response = '';

        $user = UserModel::find($applicantId);
        if($user instanceof UserModel && $user->type == \App\Enums\UserTypeEnum::Applicant ) {
            $request = $user->requests()->where('id',$requestId)->first();

            if($request instanceof RequestModel) {
                $response = response()->json(['request' => new RequestResource($request)], 200);
            }
            else {
                $response = response()->json(['message' => 'Request not found.'], 404);
            }
        }
        else {
            $response = response()->json(['message' => 'Applicant not found.'], 404);
        }

        return $response;
    }



    /**
     * @OA\Get(
     *     path="/api/requests/filter-requests",
     *     summary="Get all requests by filter",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="payment_methods[]",
     *         in="query",
     *         description="Filter requests by payment methods",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="integer")
     *         ),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="request_status",
     *         in="query",
     *         description="Filter requests by status",
     *         @OA\Schema(type="string"),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="trade_volume_min",
     *         in="query",
     *         description="Filter requests by minimum trade volume",
     *         @OA\Schema(type="number"),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="trade_volume_max",
     *         in="query",
     *         description="Filter requests by maximum trade volume",
     *         @OA\Schema(type="number"),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Sort requests by order",
     *         @OA\Schema(type="string", enum={"asc", "desc"}),
     *         style="form"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function getAllRequestsByFilter(Request $request)
    {
        // Log::debug($request->all());

        $query = RequestModel::with('paymentMethods');

        // Filter requests by PaymentMethods
        if ($request->has('payment_methods')) {
            $paymentMethods = $request->input('payment_methods');
            $query->whereHas('paymentMethods', function ($q) use ($paymentMethods) {
                $q->whereIn('payment_methods.id', $paymentMethods);
            });
        }

        // Filter requests by status
        if ($request->has('request_status')) {
            $status = $request->input('request_status');
            $query->where('status', $status);
        }

        // Filter requests by trade_volume
        if ($request->has('trade_volume_min')) {
            $min = $request->input('trade_volume_min');
            $query->where('trade_volume', '>=', $min);
        }

        if ($request->has('trade_volume_max')) {
            $max = $request->input('trade_volume_max');
            $query->where('trade_volume', '<=', $max);
        }

        // Sort requests by order
        if ($request->has('order')) {
            $order = $request->input('order');
            $query->orderBy('created_at', $order);
        }

        $requests = $query->get();

        return response()->json(['requests' =>  RequestResource::collection($requests)], 200);
    }


    public function create(Request $request){

        // For Sell Request: 1) The Requester must have Rial Linked Payment Method & 2) The Requester must define Request Payment Methods List


        // For Buy Request: 1) The Requester must define Request Payment Methods List

        $euro_daily_rate = config('constants.Euro_Daily_Rate');

        return response()->json(['euro_daily_rate' =>  $euro_daily_rate], 200);


    }

}
