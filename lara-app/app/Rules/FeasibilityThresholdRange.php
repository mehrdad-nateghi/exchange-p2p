<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FeasibilityThresholdRange implements Rule
{
    public function passes($attribute, $value)
    {
        // Retrieve other relevant values from the request
        $lowerThreshold = request('lower_bound_feasibility_threshold');
        $upperThreshold = request('upper_bound_feasibility_threshold');

        // Check if the value is within the specified range
        return $value >= $lowerThreshold && $value <= $upperThreshold;
    }

    public function message()
    {
        return 'The :attribute must be greater than the lower bound and less than the upper bound.';
    }
}
