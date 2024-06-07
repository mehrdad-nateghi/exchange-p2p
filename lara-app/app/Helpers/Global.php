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
            ->message(trans('api-messages.internal_server_error'))
            ->getApiResponse();
    }
}
/////////////////////////////////////////////////////
if (!function_exists('generatePaginationParams')) {

    /**
     * Generate Pagination Parameters
     *
     * @param $resourceCollection
     * @return array
     */
    function generatePaginationParams($resourceCollection): ?array
    {
        try {
            return [
                'current_page' => $resourceCollection->currentPage(),
                'first_page_url' => $resourceCollection->url(1),
                'from' => $resourceCollection->firstItem(),
                'last_page' => $resourceCollection->lastPage(),
                'last_page_url' => $resourceCollection->url($resourceCollection->lastPage()),
                'next_page_url' => $resourceCollection->nextPageUrl(),
                'path' => $resourceCollection->path(),
                'per_page' => $resourceCollection->perPage(),
                'prev_page_url' => $resourceCollection->previousPageUrl(),
                'to' => $resourceCollection->lastItem(),
                'total' => $resourceCollection->total(),
            ];
        } catch (Throwable $e) {
            return null;
        }
    }
}