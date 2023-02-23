<?php

namespace Tests\Feature;

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
/*         $user = User::factory()->create(['type'=>UserTypeEnum::Applicant]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $bid = Bid::factory()->create(['applicant_id'=>$user->id, 'request_id'=>$request->id]);

        $this->assertTrue($request->bids->contains($bid)); */
    }
}
