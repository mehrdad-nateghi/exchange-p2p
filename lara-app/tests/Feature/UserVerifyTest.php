<?php

namespace Tests\Feature;

use App\Enums\UserRoleEnum;
use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserVerifyTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $userVerify;

    protected function setUp(): void
    {
        Parent::setup();

        $this->user = User::factory()->create(['role'=>UserRoleEnum::Applicant]);
        $this->userVerify = UserVerify::factory()->create(['user_id'=> $this->user->id]);
    }

    /** @test for the 1 to 1 User - UserVerify relation*/
    public function a_userverify_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->userVerify->user);
    }
}
