<?php

namespace App\Interfaces;


interface AuthRepositoryInterface
{
    public function setAccessTokenInCookie($accessToken);
    public function getTokenFromCookie($request);

}
