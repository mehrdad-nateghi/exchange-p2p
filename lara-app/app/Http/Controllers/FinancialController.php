<?php

namespace App\Http\Controllers;

use App\Models\Financial;
use Illuminate\Http\Request;

class FinancialController extends Controller
{

    // Calculate feasibility range [Lower Bound, Upper Bound]
    public function getFeasibilityRange(){

        $euro_daily_rate = config('constants.Euro_Daily_Rate');

        $financial_info = Financial::first();

        $result = [];

        if($financial_info instanceof Financial){
            $band_percentage = $financial_info->feasibility_band_percentage;
            $lower_bound = $euro_daily_rate - ($euro_daily_rate * $band_percentage / 100);
            $upper_bound = $euro_daily_rate + ($euro_daily_rate * $band_percentage / 100);

            $result['feasibility_range'] = ['lower_bound'=>$lower_bound, 'upper_bound'=>$upper_bound];
            $result['status'] = '200';

            return $result;
        }

        $result['feasibility_range'] = Null;
        $result['status'] = '404';
        $result['message'] = 'Financial information not found!';

        return $result;
    }
}
