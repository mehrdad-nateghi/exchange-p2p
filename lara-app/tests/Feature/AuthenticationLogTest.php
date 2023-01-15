<?php

namespace Tests\Feature;

use App\Models\AuthenticationLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationLogTest extends TestCase
{
    /** @test for the 1 to n User - AuthenticationLog relation*/
    public function an_authenticationlog_belongs_to_a_user()
    {
        $user = User::factory()->create(['type'=>'1']);
        $authenticationLog = AuthenticationLog::factory()->create(['applicant_id'=> $user->id]);

        $this->assertInstanceOf(User::class, $authenticationLog->user);
    }
}
