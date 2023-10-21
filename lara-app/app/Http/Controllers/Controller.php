<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(title="Welcome to PayLibero's APIs Panel!", description="This is a documentation covers all the available services at the backend of the project. For further information about the parameters, please feel free to explore  the Schema part of each API. The Schema part is available for POST and PUT HTTP services.", version="0.1")
 * @OA\Get(
 *     path="/",
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         response=400,
 *         description="The specified user ID is invalid (not a number).",
 *     )
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     name="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
