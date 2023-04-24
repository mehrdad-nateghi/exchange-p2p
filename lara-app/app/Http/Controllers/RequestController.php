<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequestResource;
use App\Models\Request as RequestModel;
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
}
