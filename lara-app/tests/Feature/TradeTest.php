<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\Bid;
use App\Models\Request;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TradeTest extends TestCase
{
    /*     use RefreshDatabase, WithFaker;
 */
    /** @test for the 1 to n Request - Trade relation*/
    public function a_trade_belongs_to_a_request()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id' => $request->id, 'bid_id' => $bid->id]);

        $this->assertInstanceOf(Request::class, $trade->request);
    }

    /** @test for the 1 to 1 Bid - Trade relation*/
    public function a_trade_belongs_to_a_bid()
    {
        $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);
        $trade = Trade::factory()->create(['request_id' => $request->id, 'bid_id' => $bid->id]);

        $this->assertInstanceOf(Bid::class, $trade->bid);
    }

}
