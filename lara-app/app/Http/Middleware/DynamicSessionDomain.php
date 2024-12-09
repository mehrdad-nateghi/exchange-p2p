<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DynamicSessionDomain
{
    protected array $allowedDomains;

    public function __construct()
    {
        $this->allowedDomains = array_filter(
            explode(',', env('ALLOWED_SESSION_DOMAINS', ''))
        );
    }

    public function handle(Request $request, Closure $next)
    {
        $sessionDomain = $this->validateAndGetDomain($request->getHost());
        Log::info("host: {$request->getHost()}");
        Log::info("sessionDomain: $sessionDomain");

        Config::set('session.domain', $sessionDomain);

        return $next($request);
    }

    private function validateAndGetDomain(?string $domain): ?string
    {
        if (!$domain) {
            return null;
        }

        foreach ($this->allowedDomains as $allowedDomain) {
            if (str_ends_with($domain, $allowedDomain)) {
                return ".$allowedDomain";
            }
        }

        return null;
    }
}
