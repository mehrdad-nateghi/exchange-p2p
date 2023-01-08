<?php

namespace Tests\Unit;

use App\Models\Country;
use App\Models\PaymentMethod;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CountryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_country_has_many_paymentmethods()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $this->assertTrue($country->paymentMethods->contains($paymentMethod));
    }
}
