<?php

use App\Services\Global\ApiResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
/////////////////////////////////////////////////////
if (!function_exists('getMinMaxAllowedPrice')) {
    function getMinMaxAllowedPrice(): ?array
    {
        // call third party to get current euro price
        // if there's in cache get it if not add it
        return Cache::remember('min_max_allowed_price', now()->addHours(1), function () {
            try {
                $response = Http::retry(10)->get('http://api.navasan.tech/latest/', [
                    'api_key' => 'freeEgW0lQWS0DE1pFyUoPLQgln1aLzu',
                    'item' => 'eur'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $euroPrice = $data['eur']['value'] ?? null;

                    if ($euroPrice === null) {
                        return [];
                    }

                    return [
                        'min' => $euroPrice * 0.9,  // Example: 10% below current price
                        'max' => $euroPrice * 1.1,  // Example: 10% above current price
                    ];
                }
            } catch (\Throwable $t) {
                Log::error($t);
                return [];
            }
        });
    }
}
