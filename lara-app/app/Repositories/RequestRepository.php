<?php

namespace App\Repositories;

use App\Enums\LinkedMethodStatusEnum;
use App\Interfaces\RequestRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Request;
use App\Models\User;

class RequestRepository implements RequestRepositoryInterface
{

    public function getPeerLinkedMethods($applicant, $paymentMethods)
    {
        $linked_methods = [];
        foreach($paymentMethods as $pm) {
            $lm = $applicant->linkedMethods()->where('method_type_id', $pm)->first();
            $request_linked_methods[] = $lm->id;
        }

    }



}
