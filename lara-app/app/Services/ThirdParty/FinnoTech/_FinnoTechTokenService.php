<?php

namespace App\Services\ThirdParty\FinnoTech;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class _FinnoTechTokenService
{
    private const CLIENT_CREDENTIALS_TOKEN_CACHE_KEY = 'finnotech_client_credentials_token';
    private const AUTHORIZATION_CODE_TOKEN_CACHE_KEY = 'finnotech_authorization_code_token';
    private const TOKEN_REFRESH_BUFFER_MINUTES = 5;

    public static function getClientCredentialsToken(): ?string
    {
        try {
            if (Cache::has(self::CLIENT_CREDENTIALS_TOKEN_CACHE_KEY)) {
                $tokenData = Cache::get(self::CLIENT_CREDENTIALS_TOKEN_CACHE_KEY);
                return $tokenData['value'] ?? null;
            }

            return self::refreshAndCacheClientCredentialsToken();
        } catch (\Throwable $t) {
            Log::error('FinnoTech Token Error: ' . $t->getMessage());
            return null;
        }
    }

    public static function getAuthorizationCodeTokenToken(): ?string
    {
        try {
            // Check if we have a valid cached token
            if (Cache::has(self::AUTHORIZATION_CODE_TOKEN_CACHE_KEY)) {
                Log::info('has cache');

                $tokenData = Cache::get(self::AUTHORIZATION_CODE_TOKEN_CACHE_KEY);
                return $tokenData['value'] ?? null;
            }

            Log::info('no cache');
            return self::refreshAndCacheAuthorizationCodeToken();
        } catch (\Throwable $t) {
            Log::error('FinnoTech Token Error: ' . $t->getMessage());
            return null;
        }
    }

    private static function refreshAndCacheClientCredentialsToken(): ?string
    {
        $clientId = config('finnotech.client_id');
        $clientSecret = config('finnotech.client_secret');
        $baseUrl = config('finnotech.base_url');
        $nid = config('finnotech.national_id');

        // Create Basic Auth string
        $authString = self::getBase64_encode($clientId, $clientSecret);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic {$authString}"
            ])->post($baseUrl . '/dev/v2/oauth2/token', [
                'grant_type' => 'client_credentials',
                'nid' => $nid,
                'scopes' => config('finnotech.credentials_token_scopes') // Adjust scopes: a,b,c
            ]);

            $a = $response->json();

            if (!$response->successful()) {
                Log::error('FinnoTech Token Error Response: ' . $response->body());
                return null;
            }

            $responseData = $response->json();

            if ($responseData['status'] !== 'DONE') {
                Log::error('FinnoTech Token Error: Status is not DONE', $responseData);
                return null;
            }

            $tokenData = $responseData['result'];

            // Calculate cache duration from lifeTime (converting from milliseconds to minutes)
            $cacheDuration = self::calculateCacheDurationInMinutes($tokenData['lifeTime'] ?? 864000000);

            // Cache the token data
            Cache::put(self::CLIENT_CREDENTIALS_TOKEN_CACHE_KEY, $tokenData, $cacheDuration * 60); // Convert minutes to seconds for Cache

            return $tokenData['value'];
        } catch (\Throwable $t) {
            Log::error('FinnoTech Token Refresh Error: ' . $t->getMessage());
            return null;
        }
    }

    private static function refreshAndCacheAuthorizationCodeToken(): ?string
    {
        $clientId = config('finnotech.client_id');
        $clientSecret = config('finnotech.client_secret');
        $baseUrl = config('finnotech.base_url');
        $authorizationCode = config('finnotech.authorization_code');

        // Create Basic Auth string
        $authString = self::getBase64_encode($clientId, $clientSecret);

        Log::info('refreshAndCacheAuthorizationCodeToken', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'authorization_code' => $authorizationCode,
            'base_url' => $baseUrl,
            'authString' => $authString
        ]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic {$authString}"
            ])->post($baseUrl . '/dev/v2/oauth2/token', [
                'grant_type' => 'authorization_code',
                'code' => $authorizationCode,
                'bank' => '062', // Ayandeh
                'redirect_uri' => 'https://paylibero.ir'
            ]);

            Log::info('response', [
                'response' => $response->json(),
            ]);

            if (!$response->successful()) {
                Log::error('FinnoTech Token Error Response: ' . $response->body());
                return null;
            }

            $responseData = $response->json();

            if ($responseData['status'] !== 'DONE') {
                Log::error('FinnoTech Token Error: Status is not DONE', $responseData);
                return null;
            }

            $tokenData = $responseData['result'];

            // Calculate cache duration from lifeTime (converting from milliseconds to minutes)
            $cacheDuration = self::calculateCacheDurationInMinutes($tokenData['lifeTime'] ?? 864000000);

            // Cache the token data
            Cache::put(self::AUTHORIZATION_CODE_TOKEN_CACHE_KEY, $tokenData, $cacheDuration * 60); // Convert minutes to seconds for Cache

            return $tokenData['value'];
        } catch (\Throwable $t) {
            Log::error('FinnoTech Token Refresh Error: ' . $t->getMessage());
            return null;
        }
    }

    private static function calculateCacheDurationInMinutes(int $lifetimeMilliseconds): int
    {
        // Convert milliseconds to minutes and subtract buffer
        return (int) (($lifetimeMilliseconds / 1000) / 60) - self::TOKEN_REFRESH_BUFFER_MINUTES;
    }

    /**
     * @param mixed $clientId
     * @param mixed $clientSecret
     * @return string
     */
    public static function getBase64_encode(mixed $clientId, mixed $clientSecret): string
    {
        return base64_encode("{$clientId}:{$clientSecret}");
    }

    private static function refreshTokenUsingRefreshToken(string $refreshToken): ?string
    {
        // Implement refresh token logic if needed
        // This would be used when the original token expires
        return null;
    }
}
