<?php

use App\Services\Global\ApiResponseService;

/////////////////////////////////////////////////////
if (!function_exists('apiResponse')) {
    function apiResponse(): ApiResponseService
    {
        return new ApiResponseService();
    }
}
/////////////////////////////////////////////////////
//if (!function_exists('generateVerificationCode')) {
//    /**
//     * @throws Exception
//     */
//    function generateVerificationCode(): string
//    {
//        return Crypt::encryptString(random_int(100000, 999999));
//    }
//}
/////////////////////////////////////////////////////