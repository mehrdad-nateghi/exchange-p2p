<?php

namespace App\Services\ThirdParty\FinnoTech;

use App\Enums\FinnotechTokenTypeEnum;
use App\Models\FinnotechToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinnotechTokenService
{
    private const CLIENT_CREDENTIALS_CACHE_KEY = 'finnotech_client_credentials_token';
    private const AUTHORIZATION_CODE_TOKEN_CACHE_KEY = 'finnotech_authorization_code_token';

    public const TOKEN_REFRESH_BUFFER_MINUTES = 60;
    private const TOKEN_LIFETIME_DAYS = 10;
    private const TOKEN_LIFETIME_MILLISECONDS = 864000000; // 10 days

    /**
     * Get a valid client credentials token
     */
    public static function getClientCredentialsToken(): ?string
    {
        try {
            // Step 1: Try to get from cache first
            $cachedToken = self::getClientCredentialsTokenFromCache();
            if ($cachedToken) {
                Log::info('Finnotech client credentials token found in cache', [
                    'expires_in' => $cachedToken->getRemainingTime()
                ]);
                return $cachedToken->access_token;
            }

            // Step 2: If not in cache, try to get from database
            $dbToken = self::getClientCredentialsTokenFromDatabase();
            if ($dbToken) {
                Log::info('Finnotech client credentials token found in database', [
                    'expires_in' => $dbToken->getRemainingTime()
                ]);
                self::storeTokenInCache($dbToken);
                return $dbToken->access_token;
            }

            // Step 3: If no valid token exists, get a new one
            Log::info('Getting new Finnotech client credentials token');
            return self::getNewClientCredentialsToken();

        } catch (\Throwable $t) {
            Log::error('Finnotech Client Credentials Token Error: ' . $t->getMessage(), [
                'exception' => $t
            ]);
            return null;
        }
    }

    /**
     * Get a valid authorization token for a specific national ID
     */
    public static function getAuthorizationToken(): ?string
    {
        try {
            // Step 1: Try to get from cache first
            $cachedToken = self::getAuthorizationTokenFromCache();
            if ($cachedToken) {
                Log::info('Finnotech authorization token found in cache', [
                    'national_id' => config('finnotech.national_id'),
                    'expires_in' => $cachedToken->getRemainingTime()
                ]);
                return $cachedToken->access_token;
            }

            // Step 2: If not in cache, try to get from database
            $dbToken = self::getAuthorizationTokenFromDatabase();
            if ($dbToken) {
                /*Log::info('Finnotech authorization token found in database', [
                    'national_id' => $nationalId,
                    'expires_in' => $dbToken->getRemainingTime()
                ]);*/
                self::storeTokenInCache($dbToken);
                return $dbToken->access_token;
            }

            // Step 3: If no valid token exists, get a new one
            /*Log::info('Getting new Finnotech authorization token', [
                'national_id' => $nationalId
            ]);*/
            return self::getNewAuthorizationToken();

        } catch (\Throwable $t) {
            Log::error('Finnotech Authorization Token Error: ' . $t->getMessage(), [
                'national_id' => $nationalId,
                'exception' => $t
            ]);
            return null;
        }
    }

    private static function getClientCredentialsTokenFromCache(): ?FinnotechToken
    {
        if (!Cache::has(self::CLIENT_CREDENTIALS_CACHE_KEY)) {
            return null;
        }

        $tokenUlid = Cache::get(self::CLIENT_CREDENTIALS_CACHE_KEY);
        $token = FinnotechToken::where('ulid', $tokenUlid)->first();

        if (!$token) {
            Cache::forget(self::CLIENT_CREDENTIALS_CACHE_KEY);
            return null;
        }

        if ($token->needsRefresh()) {
            return self::refreshToken($token);
        }

        return $token;
    }

    private static function getAuthorizationTokenFromCache(): ?FinnotechToken
    {
        $cacheKey = self::AUTHORIZATION_CODE_TOKEN_CACHE_KEY;

        if (!Cache::has($cacheKey)) {
            return null;
        }

        $tokenUlid = Cache::get($cacheKey);
        $token = FinnotechToken::where('ulid', $tokenUlid)->first();

        if (!$token) {
            Cache::forget($cacheKey);
            return null;
        }

        if ($token->needsRefresh()) {
            return self::refreshToken($token);
        }

        return $token;
    }

    private static function getClientCredentialsTokenFromDatabase(): ?FinnotechToken
    {
        $token = FinnotechToken::query()
            ->clientCredentials()
            ->valid()
            ->latest()
            ->first();

        if (!$token) {
            return null;
        }

        if ($token->needsRefresh()) {
            return self::refreshToken($token);
        }

        return $token;
    }

    private static function getAuthorizationTokenFromDatabase(): ?FinnotechToken
    {
        $token = FinnotechToken::query()
            ->authorizationCode()
            //->where('national_id', $nationalId)
            ->valid()
            ->latest()
            ->first();

        if (!$token) {
            return null;
        }

        if ($token->needsRefresh()) {
            return self::refreshToken($token);
        }

        return $token;
    }

    private static function getNewClientCredentialsToken(): ?string
    {
        $response = self::makeTokenRequest([
            'grant_type' => 'client_credentials',
            'nid' => config('finnotech.national_id'),
            'scopes' => config('finnotech.credentials_token_scopes')
        ]);

        if (!$response || $response['status'] !== 'DONE') {
            Log::error('Failed to get new client credentials token', [
                'response' => $response ?? 'null'
            ]);
            return null;
        }

        $token = self::storeNewToken(
            FinnotechTokenTypeEnum::CLIENT_CREDENTIALS,
            $response['result'],
        );

        return $token->access_token;
    }

    private static function getNewAuthorizationToken(): ?string
    {
        $authorizationCode = config('finnotech.authorization_code');

        $response = self::makeTokenRequest([
            'grant_type' => 'authorization_code',
            'code' => $authorizationCode,
            'bank' => '062', // Ayandeh
            'redirect_uri' => 'https://paylibero.ir'

            /*'grant_type' => 'authorization_code',
            'nid' => config('finnotech.national_id'),
            'scopes' => config('finnotech.scopes')*/
        ]);

        if (!$response || $response['status'] !== 'DONE') {
            Log::error('Failed to get new authorization token', [
                'national_id' => $nationalId,
                'response' => $response ?? 'null'
            ]);
            return null;
        }

        $token = self::storeNewToken(
            FinnotechTokenTypeEnum::AUTHORIZATION_CODE,
            $response['result'],
        );

        return $token->access_token;
    }

    private static function refreshToken(FinnotechToken $token): ?FinnotechToken
    {
        Log::info('Attempting to refresh token', [
            'type' => $token->token_type->name,
            'ulid' => $token->ulid
        ]);

        $b = $token->token_type;
        $a = $token->token_type->name;

        $response = self::makeTokenRequest([
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->refresh_token,
            'token_type' => "CLIENT-CREDENTIAL"
        ]);

        if (!$response || $response['status'] !== 'DONE') {
            Log::error('Token refresh failed', [
                'type' => $token->token_type->name,
                'response' => $response ?? 'null'
            ]);
            $token->deactivate();
            return null;
        }

        return self::updateToken($response['result'], $token);
    }

    private static function storeNewToken(
        FinnotechTokenTypeEnum $tokenType,
        array $tokenData
    ): FinnotechToken {

        $expiresAt = now()->addSeconds($tokenData['lifeTime'] / 1000);

        $token = FinnotechToken::create([
            'token_type' => $tokenType,
            'access_token' => $tokenData['value'],
            'refresh_token' => $tokenData['refreshToken'] ?? null,
            'scopes' => $tokenData['scopes'] ?? [],
            'national_id' => config('finnotech.national_id'),
            'lifetime' => $tokenData['lifeTime'],
            'expires_at' => $expiresAt,
            'is_active' => true,
            'metadata' => [
                'created_via' => 'api_request',
                'initial_creation' => true
            ]
        ]);

        self::storeTokenInCache($token);

        /*Log::info('New token stored', [
            'type' => $tokenType->name,
            'national_id' => $nationalId,
            'ulid' => $token->ulid,
            'expires_at' => $token->expires_at,
            'remaining_time' => $token->getRemainingTime()
        ]);*/

        return $token;
    }

    private static function storeTokenInCache(FinnotechToken $token): void
    {
        $cacheKey = $token->token_type->value === FinnotechTokenTypeEnum::CLIENT_CREDENTIALS->value
            ? self::CLIENT_CREDENTIALS_CACHE_KEY
            : self::AUTHORIZATION_CODE_TOKEN_CACHE_KEY;

        $cacheDuration = Carbon::now()->diffInSeconds($token->expires_at);

        // Debug the actual cache key
        /*Log::info('Cache storage details', [
            'original_key' => $cacheKey,
            'full_cache_key' => Cache::getPrefix() . $cacheKey,
            'cache_prefix' => Cache::getPrefix(),
            'duration' => $cacheDuration
        ]);*/

        Cache::put($cacheKey, $token->ulid, $cacheDuration);
    }

    private static function makeTokenRequest(array $params): ?array
    {
        $baseUrl = config('finnotech.base_url');
        $c = config('finnotech.client_id');
        $d = config('finnotech.client_secret');

        $authString = base64_encode(
            config('finnotech.client_id') . ':' . config('finnotech.client_secret')
        );

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic {$authString}"
            ])->post($baseUrl . '/dev/v2/oauth2/token', $params);

            $a = $response->body();
            if (!$response->successful()) {
                Log::error('Finnotech API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            return $response->json();

        } catch (\Throwable $t) {
            Log::error('Finnotech API Request Error: ' . $t->getMessage());
            return null;
        }
    }

    /**
     * @param $result
     * @param FinnotechToken $token
     * @return FinnotechToken
     */
    public static function updateToken($result, FinnotechToken $token): FinnotechToken
    {
        $tokenData = $result;
        $expiresAt = now()->addSeconds($tokenData['lifeTime'] / 1000);

        // Update existing token instead of creating new one
        $token->update([
            'access_token' => $tokenData['value'],
            'refresh_token' => $tokenData['refreshToken'] ?? $token->refresh_token,
            'scopes' => $tokenData['scopes'] ?? $token->scopes,
            'lifetime' => $tokenData['lifeTime'],
            'expires_at' => $expiresAt,
            'refresh_count' => $token->refresh_count + 1,
            'metadata' => array_merge($token->metadata ?? [], [
                'last_refreshed_at' => now()->toDateTimeString(),
            ])
        ]);

        self::storeTokenInCache($token);

        return $token;
    }
}
