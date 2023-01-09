<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\MethodAttribute;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MethodAttributeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test for the 1 to n paymentmethod - methodattribute relation*/
    public function a_methodattribute_belongs_to_a_paymentmethod()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $methodAttribute = MethodAttribute::factory()->create(['payment_method_id'=>$paymentMethod->id]);

        $this->assertInstanceOf(PaymentMethod::class, $methodAttribute->paymentMethod);
    }
}
