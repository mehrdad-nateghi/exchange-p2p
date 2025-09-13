<?php

namespace App\Http\Controllers;

use App\Models\Financial;
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

    // Calculate feasibility range threshold [Lower Bound, Upper Bound] use in Request-related functionalities
    public function getFeasibilityRange(){

        $euro_daily_rate = config('constants.Euro_Daily_Rate');

        $financial_info = Financial::first();

        $result = [];

        if($financial_info instanceof Financial && $euro_daily_rate != Null){
            $band_percentage = $financial_info->feasibility_band_percentage;
            $lower_bound = $euro_daily_rate - ($euro_daily_rate * $band_percentage / 100);
            $upper_bound = $euro_daily_rate + ($euro_daily_rate * $band_percentage / 100);

            $result['feasibility_range'] = ['lower_bound'=>$lower_bound, 'upper_bound'=>$upper_bound];
            $result['status'] = '200';

            return $result;
        }

        $result['feasibility_range'] = Null;
        $result['status'] = '404';
        $result['message'] = 'Financial information or euro daily rate not found!';

        return $result;
    }
}
