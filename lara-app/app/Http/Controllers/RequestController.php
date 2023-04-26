<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequestResource;
use App\Models\Request as RequestModel;
use App\Models\User as UserModel;
use Illuminate\Http\Request;

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
}
