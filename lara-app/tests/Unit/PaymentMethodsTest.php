<?php

namespace Tests\Unit;

use App\Models\Country;
use App\Models\PaymentMethod;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PaymentMethodsTest extends TestCase
{
    use  RefreshDatabase, WithFaker;

    /** @test */
    public function a_paymentmethod_belongs_to_a_country()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);

        // $this->assertEquals(1, $paymentMethod->country->count());
        $this->assertInstanceOf(Country::class, $paymentMethod->country);
    }

}
