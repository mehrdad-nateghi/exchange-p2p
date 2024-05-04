<?php

namespace Tests\Feature;

use App\Enums\old\UserRoleEnum;
use App\Models\Bid;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\LinkedMethod;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class LinkedMethodTest extends TestCase
{
    use RefreshDatabase;

    protected $country;
    protected $paymentMethod;
    protected $user;
    protected $linkedMethod;
    protected $request;
    protected $bid;
    protected $trade;
    protected $invoice;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->country = Country::factory()->create();
        $this->paymentMethod = PaymentMethod::factory()->create(['country_id' => $this->country->id]);
        $this->user = User::factory()->create(['role'=>UserRoleEnum::Applicant]);
        $this->linkedMethod = LinkedMethod::factory()->create(['method_type_id'=>$this->paymentMethod->id, 'applicant_id'=>$this->user->id]);
        $this->request = Request::factory()->create(['applicant_id' => $this->user->id]);
        $this->bid = Bid::factory()->create(['applicant_id'=>$this->user->id, 'request_id'=>$this->request->id, 'target_account_id'=>$this->linkedMethod->id]);
        $this->trade = Trade::factory()->create(['request_id'=>$this->request->id, 'bid_id'=>$this->bid->id]);
        $this->invoice = Invoice::factory()->create(['applicant_id'=>$this->user->id, 'trade_id'=>$this->trade->id]);
    }

    /** @test for the 1 to n PaymentMethod - LinkedMethod relation*/
    public function a_linkedmethod_belongs_to_a_paymentmethod()
    {
        $this->assertInstanceOf(PaymentMethod::class, $this->linkedMethod->paymentMethod);
    }

    /** @test for the m to n LinkedMethod - MethodAttribute relation*/
    public function a_linkedmethod_belongs_to_many_methodattributes()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->linkedMethod->attributes);
    }

    /** @test for the 1 to n User - LinkedMethod relation*/
    public function a_linkedmethod_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->linkedMethod->user);
    }

    /** @test for the 1 to n LinkedMethod - Invoice relation*/
    public function a_linkedmethod_has_many_bids()
    {
        $this->assertTrue($this->linkedMethod->bids->contains($this->bid));
    }

    /** @test for the m to n Request - LinkedMethod relation*/
    public function a_linkedmethod_belongs_to_many_requests()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->linkedMethod->requests);
    }
}
