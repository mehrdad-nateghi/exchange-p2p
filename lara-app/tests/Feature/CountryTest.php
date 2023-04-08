<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use RefreshDatabase;

    protected $country;
    protected $paymentMethod;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->country = Country::factory()->create();
        $this->paymentMethod = PaymentMethod::factory()->create(['country_id' => $this->country->id]);
    }

    /** @test for the 1 to n Country - PaymentMethod relation*/
    public function a_country_has_many_paymentmethods()
    {

        $this->assertTrue($this->country->paymentMethods->contains($this->paymentMethod));
    }
}
