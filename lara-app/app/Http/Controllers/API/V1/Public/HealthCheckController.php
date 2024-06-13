<?php

namespace App\Http\Controllers\API\V1\Public;

use App\Http\Controllers\Controller;
use App\Jobs\TestJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class HealthCheckController extends Controller
{
    public function __invoke(): JsonResponse {
        try {

            $checks = [];

            // Check database connectivity
            try {
                DB::selectOne('SELECT 1');
                $checks['database'] = 'ok';
            } catch (\Exception $e) {
                Log::error($e);
                $checks['database'] = 'failed';
            }

            // Check Redis connectivity
            try {
                Redis::ping();
                $checks['redis'] = 'ok';
            } catch (\Exception $e) {
                Log::error($e);
                $checks['redis'] = 'failed';
            }

            // Check cache connectivity
            try {
                Cache::store()->get('test_cache_key');
                $checks['cache'] = 'ok';
            } catch (\Exception $e) {
                Log::error($e);
                $checks['cache'] = 'failed';
            }

            // Check queue connectivity
            try {
                TestJob::dispatch();
                sleep(3);

                if (Cache::has('health_check_queue')) {
                    $checks['queue'] = 'ok';
                } else {
                    $checks['queue'] = 'failed';
                }
            } catch (\Exception $e) {
                Log::error($e);
                $checks['queue'] = 'failed';
            }

            // Determine the overall status
            $overallStatus = array_search('failed', $checks) ? 'failed' : 'ok';

            $data = [
                'overall_status' => $overallStatus,
                'checks' => $checks,
            ];

            if ($overallStatus === 'ok') {
                return apiResponse()
                    ->message(trans('api-messages.system_health_check_passed'))
                    ->data($data)
                    ->getApiResponse();
            }

            return apiResponse()
                ->failed()
                ->serverError()
                ->message(trans('api-messages.system_health_check_failed'))
                ->data($data)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}