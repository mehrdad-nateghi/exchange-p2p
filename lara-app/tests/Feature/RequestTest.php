<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Bid;
use App\Models\Country;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequestTest extends TestCase
{
/*     use RefreshDatabase, WithFaker;
 */
    /** @test for the m to n Request - PaymentMethod relation*/
    public function a_request_belongs_to_many_paymentmethod()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $request->paymentMethods()->attach($paymentMethod);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $request->paymentMethods);
    }

    /** @test for the 1 to n User - Request relation*/
    public function a_request_belongs_to_a_user()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);

        $this->assertInstanceOf(User::class, $request->user);
    }

    /** @test for the 1 to n Request - Bid relation*/
    public function a_request_has_many_bids()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);

        $this->assertTrue($request->bids->contains($bid));
    }
}
