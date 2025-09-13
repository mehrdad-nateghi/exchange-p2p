<?php

namespace Database\Seeders;

use App\Models\LinkedMethod;
use App\Models\MethodAttribute;
use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LinkedmethodMethodattributeSeeder extends Seeder
{
    public function run()
    {
        LinkedMethod::take(5)->each(function($linkedMethod){
            $linkedMethod->attributes()->attach(MethodAttribute::factory()->count(2)->create(), ['value'=> Str::random(7)]);
        });
    }
}
