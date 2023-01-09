<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test for the 1 to n Country - PaymentMethod relation*/
    public function a_country_has_many_paymentmethods()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $this->assertTrue($country->paymentMethods->contains($paymentMethod));
    }
}
