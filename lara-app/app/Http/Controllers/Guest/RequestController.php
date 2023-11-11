<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequestResource;
use App\Models\Request as RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
     *     path="/api/requests/filter",
     *     summary="Get all requests by filter",
     *     tags={"Requests"},
     *     operationId="getAllRequestsByFilter",
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter requests by type (sell or buy)",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             enum={"0", "1"}
     *         ),
     *        description="0: Buy, 1: Sell"
     *     ),
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
     *     @OA\Parameter(
     *         name="count",
     *         in="query",
     *         description="filter requests by count",
     *         @OA\Schema(type="number"),
     *         style="form"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         )
     *     ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function getRequests(Request $request)
    {
        $query = RequestModel::with('linkedMethods');

        // Filter requests by type
        if ($request->has('type')) {
            $type = $request->input('type');
            $query->where('type', $type);
        }

        // Filter requests by PaymentMethods
        if ($request->has('payment_methods')) {
            $paymentMethods = $request->input('payment_methods');
            $query->whereHas('linkedMethods', function ($q) use ($paymentMethods) {
                $q->whereIn('method_type_id', $paymentMethods);
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
            $query->orderBy('id', $order);
        }

        // Filter requests by count
        if ($request->has('count')) {
            $count = $request->input('count');
            $query->take($count);
        }

        $requests = $query->get();

        return response()->json(['requests' =>  RequestResource::collection($requests)], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/requests/{requestId}",
     *     summary="Get specific request by id",
     *     tags={"Requests"},
     *     operationId="getSpecificRequest",
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
     *         response=500,
     *         description="Internal Server Error",
     *     )
     *     )
     * )
     */
    public function getRequest($requestId){

        $request = RequestModel::find($requestId);

        if(!($request instanceof RequestModel)) {
            return response()->json(['message' => 'Request not found.'], 404);
        }

        $request_bids_info = $request->bids()->with('user')->get()->map(function ($bid) {
            return [
                'bidder_id' => $bid->user->id,
                'bidder_name' => $bid->user->first_name,
                'status' => $bid->status,
                'bid_rate' => $bid->bid_rate,
                'registered_date' => $bid->created_at,
                'description' => $bid->description,
            ];
        });

        $request_payment_methods = $request->paymentMethods()
        ->select('payment_methods.id as payment_method_id', 'payment_methods.name')
        ->get()
        ->map(function ($item) {
            unset($item->pivot);
            return $item;
        });

        $data = [
            'request_id' => $request->id,
            'bids' => $request_bids_info,
            'request_payment_methods' => $request_payment_methods
        ];

        return response()->json($data, 200);
    }

}
