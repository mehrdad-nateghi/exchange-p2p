<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BidStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Request;

/**
 * @OA\Tag(
 *     name="Requests",
 *     description="APIs for managing Requests"
 * )
 */
class RequestController extends Controller
{

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

}
