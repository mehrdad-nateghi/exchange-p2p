<?php

namespace Tests\Feature;

use App\Enums\UserRoleEnum;
use App\Models\AuthenticationLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationLogTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $authenticationLog;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->user = User::factory()->create(['role'=>UserRoleEnum::Applicant]);
        $this->authenticationLog = AuthenticationLog::factory()->create(['applicant_id'=> $this->user->id]);
    }

    /** @test for the 1 to n User - AuthenticationLog relation*/
    public function an_authenticationlog_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->authenticationLog->user);
    }
}
