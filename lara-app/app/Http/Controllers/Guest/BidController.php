<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\BidResource;
use App\Models\Request as RequestModel;

/**
 * @OA\Tag(
 *     name="Bids",
 *     description="APIs for managing bids"
 * )
 */
class BidController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/bids/request/{requestId}",
     *     summary="Get all bids of specific request",
     *     tags={"Bids"},
     *     @OA\Parameter(
     *         name="requestId",
     *         in="path",
     *         description="ID of the request to fetch its bids",
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
     *     )
     * )
     */
    public function getBids($requestId)
    {
        $request = RequestModel::find($requestId);

        $response = '';

        if($request instanceof RequestModel) {
            $bids = $request->bids()->get();
            $response = response()->json(['bids' => BidResource::collection($bids)], 200);
        }
        else {
            $response = response()->json(['message' => 'Request not found.'], 404);
        }

        return $response;
    }
}
