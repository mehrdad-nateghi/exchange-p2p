<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\LinkedMethod;
use App\Models\MethodAttribute;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
/*     use RefreshDatabase, WithFaker;
 */
    /** @test for the 1 to n Country - PaymentMethod relation*/
    public function a_paymentmethod_belongs_to_a_country()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);

        $this->assertInstanceOf(Country::class, $paymentMethod->country);
    }

    /** @test for the 1 to n PaymentMethod - MethodAttribute relation*/
    public function a_paymentmethod_has_many_methodattributes()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $methodAttribute = MethodAttribute::factory()->create(['payment_method_id'=>$paymentMethod->id]);

        $this->assertTrue($paymentMethod->attributes->contains($methodAttribute));
    }

    /** @test for the 1 to n PaymentMethod - LinkedMethod relation*/
    public function a_paymentmethod_has_many_linkedmethods()
    {
        $country = Country::factory()->create();
        $applicant = User::factory()->create(['type'=>'1']);
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$applicant->id]);

        $this->assertTrue($paymentMethod->linkedMethods->contains($linkedMethod));
    }

    /** @test for the m to n Request - PaymentMethod relation*/
    public function a_paymentmethod_belongs_to_many_requests()
    {
        $user = User::factory()->create(['type'=>'1']);
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $request->paymentMethods()->attach($paymentMethod);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $paymentMethod->requests);
    }
}
