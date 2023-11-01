<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\LinkPaymentMethodRequest;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function linkPaymentMethod(LinkPaymentMethodRequest $request) {

        $validatedData = $request->validated();

    }
}
