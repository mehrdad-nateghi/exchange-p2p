<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    protected function applicantSignUp(SignUpRequest $request)
    {
        $validated_credentials = $request->validated();

        $user = User::create($validated_credentials);

        event(new Registered($user));

        return response(['message' => 'Applicant registered successfully. Please check your email to verify your account.']);
    }
}
