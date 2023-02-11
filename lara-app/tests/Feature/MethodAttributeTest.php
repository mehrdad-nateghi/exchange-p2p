<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Country;
use App\Models\LinkedMethod;
use App\Models\MethodAttribute;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class MethodAttributeTest extends TestCase
{
/*     use RefreshDatabase, WithFaker;
 */
    /** @test for the 1 to n paymentmethod - methodattribute relation*/
    public function a_methodattribute_belongs_to_a_paymentmethod()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $methodAttribute = MethodAttribute::factory()->create(['payment_method_id'=>$paymentMethod->id]);

        $this->assertInstanceOf(PaymentMethod::class, $methodAttribute->paymentMethod);
    }

    /** @test for the m to n LinkedMethod - MethodAttribute relation*/
    public function a_methodattribute_belongs_to_many_linkedmethods()
    {
        $country = Country::factory()->create();
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $attribute = MethodAttribute::factory()->create(['payment_method_id'=>$paymentMethod->id]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$user->id]);
        $linkedMethod->attributes()->attach($attribute, ['value'=> Str::random(7) ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $attribute->linkedMethods);
    }


}
