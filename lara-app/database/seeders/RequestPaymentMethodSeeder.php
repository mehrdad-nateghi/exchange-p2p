<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\Request;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequestPaymentMethodSeeder extends Seeder
{
    public function run()
    {
        Request::take(5)->each(function($request){
            $request->paymentMethods()->attach(PaymentMethod::factory()->count(2)->create());
        });
    }
}
