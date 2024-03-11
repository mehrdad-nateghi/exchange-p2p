<?php

namespace App\Repositories;

use App\Interfaces\RequestRepositoryInterface;

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
