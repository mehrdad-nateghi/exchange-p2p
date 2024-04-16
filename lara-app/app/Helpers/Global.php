<?php

use App\Services\Response\ResponseService;

if (!function_exists('responseService')) {
    function responseService(): ResponseService
    {
        return new ResponseService();
    }
}