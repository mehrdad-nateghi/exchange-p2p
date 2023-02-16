<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Bid;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\LinkedMethod;
use App\Models\MethodAttribute;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;


class LinkedMethodTest extends TestCase
{
/*     use RefreshDatabase, WithFaker;
 */
    /** @test for the 1 to n PaymentMethod - LinkedMethod relation*/
    public function a_linkedmethod_belongs_to_a_paymentmethod()
    {
        $country = Country::factory()->create();
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$user->id]);

        $this->assertInstanceOf(PaymentMethod::class, $linkedMethod->paymentMethod);
    }

    /** @test for the m to n LinkedMethod - MethodAttribute relation*/
    public function a_linkedmethod_belongs_to_many_methodattributes()
    {
        $country = Country::factory()->create();
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $attribute = MethodAttribute::factory()->create(['payment_method_id'=>$paymentMethod->id]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$user->id]);
        $linkedMethod->attributes()->attach($attribute, ['value'=> Str::random(7) ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $linkedMethod->attributes);
    }

    /** @test for the 1 to n User - LinkedMethod relation*/
    public function a_linkedmethod_belongs_to_a_user()
    {
        $country = Country::factory()->create();
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$user->id]);

        $this->assertInstanceOf(User::class, $linkedMethod->user);
    }

    /** @test for the 1 to n LinkedMethod - Invoice relation*/
    public function a_linkedmethod_has_many_invoices()
    {
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$paymentMethod->id, 'applicant_id'=>$user->id]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id'=>$request->id, 'bid_id'=>$bid->id]);
        $invoice = Invoice::factory()->create(['applicant_id'=>$user->id, 'trade_id'=>$trade->id, 'target_account_id'=>$linkedMethod->id]);

        $this->assertTrue($linkedMethod->invoices->contains($invoice));
    }
}
