<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    /*     use RefreshDatabase, WithFaker;
 */

    /** @test for the 1 to n EmailTemplate - Email relation*/
    public function a_emailtemplate_has_many_emails()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $emaiTemplate = EmailTemplate::factory()->create();
        $email = Email::factory()->create(['user_id'=> $user->id, 'template_id'=>$emaiTemplate->id, 'emailable_type' => "App\Models\User", 'emailable_id' => $user->id]);

        $this->assertTrue($emaiTemplate->emails->contains($email));
    }
}
