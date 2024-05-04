<?php

namespace Tests\Feature;

use App\Enums\old\UserRoleEnum;
use App\Models\Bid;
use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\Request;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $request;
    protected $bid;
    protected $trade;
    protected $emailTemplate;
    protected $email;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role'=>UserRoleEnum::Applicant]);
        $this->request = Request::factory()->create(['applicant_id' => $this->user->id]);
        $this->bid = Bid::factory()->create(['applicant_id'=>$this->user->id, 'request_id'=>$this->request->id]);
        $this->trade = Trade::factory()->create(['request_id' => $this->request->id, 'bid_id' => $this->bid->id]);
        $this->emailTemplate = EmailTemplate::factory()->create();
        $this->email = Email::factory()->create(['user_id'=>$this->user->id, 'template_id'=>$this->emailTemplate->id, 'emailable_id' => $this->trade->id, 'emailable_type' => "App\Models\Trade"]);
    }

    /** @test for the 1 to n User - Email relation*/
    public function an_email_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->email->user);
    }

    /** @test for the 1 to n EmailTemplate - Email relation*/
    public function an_email_belongs_to_an_emailtemplate()
    {
        $this->assertInstanceOf(EmailTemplate::class, $this->email->emailTemplate);
    }

    /** @test for the 1 to n polymorph Email - User relation*/
    public function an_email_can_be_morphed_to_a_user_model()
    {
        $this->email = Email::factory()->create(['user_id'=>$this->user->id, 'template_id'=>$this->emailTemplate->id, 'emailable_id' => $this->user->id, 'emailable_type' => "App\Models\User"]);

        $this->assertInstanceOf(User::class, $this->email->emailable);
    }

    /** @test for the 1 to n polymorph Email - Request relation*/
    public function an_email_can_be_morphed_to_a_request_model()
    {
        $this->email = Email::factory()->create(['user_id'=>$this->user->id, 'template_id'=>$this->emailTemplate->id, 'emailable_id' => $this->request->id, 'emailable_type' => "App\Models\Request"]);
        $this->assertInstanceOf(Request::class, $this->email->emailable);
    }

    /** @test for the 1 to n polymorph Email - Bid relation*/
    public function an_email_can_be_morphed_to_a_bid_model()
    {
        $this->email = Email::factory()->create(['user_id'=>$this->user->id, 'template_id'=>$this->emailTemplate->id, 'emailable_id' => $this->bid->id, 'emailable_type' => "App\Models\Bid"]);

        $this->assertInstanceOf(Bid::class, $this->email->emailable);
    }

    /** @test for the 1 to n polymorph Email - Trade relation*/
    public function an_email_can_be_morphed_to_a_trade_model()
    {
        $this->email = Email::factory()->create(['user_id'=>$this->user->id, 'template_id'=>$this->emailTemplate->id, 'emailable_id' => $this->trade->id, 'emailable_type' => "App\Models\Trade"]);

        $this->assertInstanceOf(Trade::class, $this->email->emailable);
    }
}
