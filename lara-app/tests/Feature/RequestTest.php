<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequestTest extends TestCase
{

    /** @test for the m to n Request - PaymentMethod relation*/
    public function a_request_belongs_to_many_paymentmethod()
    {
        $user = User::factory()->create(['type'=>'1']);
        $country = Country::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['country_id' => $country->id]);
        $request = Request::factory()->create(['applicant_id' => $user->id]);
        $request->paymentMethods()->attach($paymentMethod);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $request->paymentMethods);
    }
}
