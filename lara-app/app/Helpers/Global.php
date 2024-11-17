<?php

use App\Services\Global\ApiResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        //Cache::forget('min_max_allowed_price');
        return Cache::remember('min_max_allowed_price', now()->addHours(6), function () {
            try {
                $response = Http::retry(10)->get('http://api.navasan.tech/latest/', [
                    'api_key' => 'freeUOZsqT8UDAg8o82kxDs9uZqmGNer',
                    'item' => 'eur'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $euroPrice = $data['eur']['value'] ?? null;

                    if ($euroPrice === null) {
                        return [];
                    }

                    return [
                        'rate' => (int) $euroPrice,
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

/////////////////////////////////////////////////////
if (!function_exists('generateUniqueNumber')) {
    /**
     * Generate a unique number for specified table and column
     *
     * @param string $table Table name to check uniqueness against
     * @param string $column Column name to check uniqueness against
     * @param int $length Length of number (default: 10)
     * @param bool $startWithZero Whether the number can start with 0 (default: false)
     * @return string
     */
    function generateUniqueNumber(string $table, string $column, int $length = 10, bool $startWithZero = false): string
    {
        do {
            if ($startWithZero) {
                $number = str_pad(mt_rand(0, str_repeat(9, $length)), $length, '0', STR_PAD_LEFT);
            } else {
                $min = pow(10, $length - 1);
                $max = pow(10, $length) - 1;
                $number = (string) mt_rand($min, $max);
            }
        } while (DB::table($table)->where($column, $number)->exists());

        return $number;
    }
}
