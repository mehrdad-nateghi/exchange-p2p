<?php

namespace Database\Seeders;

use App\Models\MethodAttribute;
use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $paymentMethods = PaymentMethod::factory(5)->create();

        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethod->attributes()->createMany(MethodAttribute::factory()->count(2)->make()->toArray());
        }

    }
}
