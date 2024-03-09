<?php

namespace App\Interfaces;


interface AuthRepositoryInterface
{
    public function setAccessTokenInCookie($accessToken);

}
