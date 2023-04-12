<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(title="My First API", description="This is test documentation", version="0.1")
 * @OA\Get(
 *     path="/",
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         response=400,
 *         description="The specified user ID is invalid (not a number).",
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
