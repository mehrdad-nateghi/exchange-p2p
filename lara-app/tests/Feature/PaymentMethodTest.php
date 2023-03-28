<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Country;
use App\Models\LinkedMethod;
use App\Models\MethodAttribute;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected $country;
    protected $paymentMethod;
    protected $user;
    protected $request;
    protected $methodAttribute;
    protected $linkedMethod;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $this->country = Country::factory()->create();
        $this->paymentMethod = PaymentMethod::factory()->create(['country_id' => $this->country->id]);
        $this->request = Request::factory()->create(['applicant_id' => $this->user->id]);
        $this->methodAttribute = MethodAttribute::factory()->create(['payment_method_id'=>$this->paymentMethod->id]);
        $this->linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$this->paymentMethod->id, 'applicant_id'=>$this->user->id]);
    }

    /** @test for the 1 to n Country - PaymentMethod relation*/
    public function a_paymentmethod_belongs_to_a_country()
    {
        $this->assertInstanceOf(Country::class, $this->paymentMethod->country);
    }

    /** @test for the 1 to n PaymentMethod - MethodAttribute relation*/
    public function a_paymentmethod_has_many_methodattributes()
    {
        $this->assertTrue($this->paymentMethod->attributes->contains($this->methodAttribute));
    }

    /** @test for the 1 to n PaymentMethod - LinkedMethod relation*/
    public function a_paymentmethod_has_many_linkedmethods()
    {
        $this->assertTrue($this->paymentMethod->linkedMethods->contains($this->linkedMethod));
    }

    /** @test for the m to n Request - PaymentMethod relation*/
    public function a_paymentmethod_belongs_to_many_requests()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->paymentMethod->requests);
    }
}
