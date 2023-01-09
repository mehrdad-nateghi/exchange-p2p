<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\MethodAttribute;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test for the 1 to n Country - PaymentMethod relation*/
    public function a_paymentmethod_belongs_to_a_country()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);

        $this->assertInstanceOf(Country::class, $paymentMethod->country);
    }

    /** @test for the 1 to n paymentmethod - methodattribute relation*/
    public function a_paymentmethod_has_many_methodattributes()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $methodAttribute = MethodAttribute::factory()->create(['payment_method_id'=>$paymentMethod->id]);

        $this->assertTrue($paymentMethod->attributes->contains($methodAttribute));
    }
}
