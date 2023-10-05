<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Http\Controllers\Applicant\RequestController as ApplicantRequestController;
use App\Models\Bid;
use App\Models\Country;
use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\Financial;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $request;
    protected $bid;
    protected $trade;
    protected $emaiTemplate;
    protected $email;
    protected $notification;
    protected $country;
    protected $paymentMethod;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->country = Country::factory()->create();
        $this->paymentMethod = PaymentMethod::factory()->create(['country_id' => $this->country->id]);
        $this->user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $this->request = Request::factory()->create(['applicant_id' => $this->user->id]);
        $this->bid = Bid::factory()->create(['applicant_id'=>$this->user->id, 'request_id'=>$this->request->id]);
        $this->emaiTemplate = EmailTemplate::factory()->create();
        $this->email = Email::factory()->create(['user_id'=>$this->user->id, 'template_id'=>$this->emaiTemplate->id, 'emailable_id' => $this->user->id, 'emailable_type' => "App\Models\Request"]);
        $this->notification = Notification::factory()->create(['user_id'=> $this->user->id, 'notifiable_id' => $this->request->id, 'notifiable_type' => "App\Models\Request"]);
    }

    /** @test for the m to n Request - PaymentMethod relation*/
    public function a_request_belongs_to_many_paymentmethod()
    {
        $this->request->paymentMethods()->attach($this->paymentMethod);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->request->paymentMethods);
    }

    /** @test for the 1 to n User - Request relation*/
    public function a_request_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->request->user);
    }

    /** @test for the 1 to n Request - Bid relation*/
    public function a_request_has_many_bids()
    {
        $this->assertTrue($this->request->bids->contains($this->bid));
    }

    /** @test for the 1 to n polymorph Email - Request relation*/
    public function a_request_morphs_many_emails()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->request->emails);
    }

    /** @test for the 1 to n polymorph Notification - Request relation*/
    public function a_request_morphs_many_notifications()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->request->notifications);
    }

    /** @test for the getFeasibilityRange success scenario*/
    public function testFeasibilityRangeWithFinancialInfo()
    {
        // Create a Financial instance in the database
        Financial::factory()->create([
            'feasibility_band_percentage' => 10,
        ]);

        // Set the config value for Euro Daily Rate
        config(['constants.Euro_Daily_Rate' => 100]);

        $controller = new ApplicantRequestController();

        $response = $controller->getFeasibilityRange();

        dump($response);

        // Assertions
        $this->assertEquals(200, $response['status']); // Check if the status is 200
    }

    /** @test for the getFeasibilityRange failure scenario*/
    public function testFeasibilityRangeWithoutFinancialInfo()
    {
        // Ensure there is no Financial instance in the database

        $controller = new ApplicantRequestController();

        $response = $controller->getFeasibilityRange();

        dump($response);

        // Assertions
        $this->assertEquals(404, $response['status']); // Check if the status is 404
    }

}
