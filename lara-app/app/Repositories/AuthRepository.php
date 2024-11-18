<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AuthRepository implements AuthRepositoryInterface
{

    public function setAccessTokenInCookie($accessToken)
    {
        // Set the 'access_token' cookie
        $response = Response::make('')->cookie(
            'access_token',
            $accessToken,
            config('constants.COOKIE_EXPIRES_IN_PER_MIN'),
            '/',
            null,
            true, // secure flag
            true // https only flag
        );

        return $response;
    }

    public function getTokenFromCookie($request) {

        return $request->cookie('access_token');
    }

}
