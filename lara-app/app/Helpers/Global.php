<?php

use App\Services\API\V1\ResponseService;

/////////////////////////////////////////////////////
if (!function_exists('responseService')) {
    function responseService(): ResponseService
    {
        return new ResponseService();
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