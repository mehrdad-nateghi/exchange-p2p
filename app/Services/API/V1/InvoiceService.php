<?php

namespace App\Services\API\V1;

use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Request;

class InvoiceService
{
    private Invoice $model;

    public function __construct(Invoice $model)
    {
        $this->model = $model;
    }

    public function pay()
    {

    }

    /*public function create($user,$data)
    {
        return $user->requests()->create($data);
    }

    public function update($request,$data)
    {
        return $request->update($data);
    }*/


}
