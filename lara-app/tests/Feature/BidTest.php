<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Bid;
use App\Models\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BidTest extends TestCase
{
/*     use RefreshDatabase, WithFaker;
 */
    /** @test for the 1 to n Country - PaymentMethod relation*/
    public function a_bid_belongs_to_a_user()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);

        $this->assertInstanceOf(User::class, $bid->user);
    }
}
