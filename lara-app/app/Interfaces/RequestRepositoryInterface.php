<?php

namespace App\Interfaces;

use App\Models\Request;
use App\Models\User;

interface RequestRepositoryInterface
{
    public function getPeerLinkedMethods(Request $request, $paymentMethods);

}
