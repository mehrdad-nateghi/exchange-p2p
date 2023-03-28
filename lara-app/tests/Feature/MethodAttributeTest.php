<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Country;
use App\Models\LinkedMethod;
use App\Models\MethodAttribute;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MethodAttributeTest extends TestCase
{
    use RefreshDatabase;

    protected $country;
    protected $paymentMethod;
    protected $user;
    protected $linkedMethod;
    protected $attribute;
    protected $methodAttribute;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->country = Country::factory()->create();
        $this->user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $this->paymentMethod = PaymentMethod::factory()->create(['country_id' => $this->country->id]);
        $this->attribute = MethodAttribute::factory()->create(['payment_method_id'=>$this->paymentMethod->id]);
        $this->linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$this->paymentMethod->id, 'applicant_id'=>$this->user->id]);
        $this->methodAttribute = MethodAttribute::factory()->create(['payment_method_id'=>$this->paymentMethod->id]);
    }

    /** @test for the 1 to n paymentmethod - methodattribute relation*/
    public function a_methodattribute_belongs_to_a_paymentmethod()
    {
        $this->assertInstanceOf(PaymentMethod::class, $this->methodAttribute->paymentMethod);
    }

    /** @test for the m to n LinkedMethod - MethodAttribute relation*/
    public function a_methodattribute_belongs_to_many_linkedmethods()
    {
        $this->linkedMethod->attributes()->attach($this->attribute, ['value'=> Str::random(7) ]);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->attribute->linkedMethods);
    }

}
