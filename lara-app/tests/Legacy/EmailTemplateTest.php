<?php

namespace Tests\Feature;

use App\Enums\Legacy\UserRoleEnum;
use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $emailTemplate;
    protected $email;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->user = User::factory()->create(['role'=>UserRoleEnum::Applicant]);
        $this->emailTemplate = EmailTemplate::factory()->create();
        $this->email = Email::factory()->create(['user_id'=> $this->user->id, 'template_id'=>$this->emailTemplate->id, 'emailable_type' => "App\Models\User", 'emailable_id' => $this->user->id]);
    }

    /** @test for the 1 to n EmailTemplate - Email relation*/
    public function a_emailtemplate_has_many_emails()
    {
        $this->assertTrue($this->emailTemplate->emails->contains($this->email));
    }
}
