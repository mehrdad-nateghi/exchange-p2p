<?php

use App\Services\Global\ApiResponseService;
use Illuminate\Http\JsonResponse;

/////////////////////////////////////////////////////
if (!function_exists('apiResponse')) {
    function apiResponse(): ApiResponseService
    {
        return new ApiResponseService();
    }
}
/////////////////////////////////////////////////////
if (!function_exists('internalServerError')) {
    function internalServerError(): JsonResponse
    {
        return apiResponse()
            ->failed()
            ->serverError()
            ->message(trans('api-message.internal_server_error'))
            ->getApiResponse();
    }
}
/////////////////////////////////////////////////////