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
     *     operationId="getAssociatedBidsOfSpecificRequest",
     *     @OA\Parameter(
     *         name="requestId",
     *         in="path",
     *         description="ID of the request to fetch its bids",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="bids", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", description = "A unique identifier or reference for a particular record or entity in the dataset."),
     *                 @OA\Property(property="support_id", type="string", description = "A unique identifier for customer support improvement purpose."),
     *                 @OA\Property(property="type", type="integer", description = "A categorical attribute that describes the nature or category of the particular bid. 0 indicates Sell bid and 1 indicates Buy bid."),
     *                 @OA\Property(property="bid_rate", type="number", format="double", description = "The quantitative measure of the suggested rate of the bid for the request." ),
     *                 @OA\Property(property="status", type="integer", description = "A descriptive attribute indicating the current state or condition of the bid. 0 indicates Registered status, 1 indicates Top status, 2 indicates Confirmed status, 3 indicates Rejected status, 4 indicates Invalid status."),
     *                 @OA\Property(property="description", type="string", description = "A textual field that contains additional information or a detailed explanation about the bid."),
     *                 @OA\Property(property="request_id", type="number", description = "An identifier associated with the request which the bid is registered on it."),
     *                 @OA\Property(property="applicant_id", type="number", description = "An identifier associated with the applicant who created the bid in the system."),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description = "The timestamp or date when the bid was initially created or entered into the system."),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description = "The timestamp or date when the bid was last modified in the system."),
     *              )
     *             ))
     *         )
     *     ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Request not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
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
