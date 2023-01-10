<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\LinkedMethod;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LinkedMethodTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test for the 1 to n PaymentMethod - LinkedMethod relation*/
    public function a_linkedmethod_belongs_to_a_paymentmethod()
    {
        $country = Country::factory()->create();
        $applicant = User::factory()->create(['type'=>'1']);
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$applicant->id]);

        $this->assertInstanceOf(PaymentMethod::class, $linkedMethod->paymentMethod);
    }

}
