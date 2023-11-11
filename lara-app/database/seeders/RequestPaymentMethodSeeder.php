<?php

namespace Database\Seeders;

use App\Models\LinkedMethod;
use App\Models\PaymentMethod;
use App\Models\Request;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequestPaymentMethodSeeder extends Seeder
{
    public function run()
    {
        Request::take(5)->each(function($request){
            $request->linkedMethods()->attach(LinkedMethod::factory()->count(2)->create());
        });
    }
}
