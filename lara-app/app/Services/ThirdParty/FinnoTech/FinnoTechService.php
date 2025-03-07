<?php

namespace App\Services\ThirdParty\FinnoTech;

use App\Enums\FinnoTechResponseStatusEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinnoTechService
{
    private string $baseUrl;
    private string $clientId;
    public ?string $clientCredentialsToken = null; // todo: change to private
    public ?string $authorizationToken = null; // todo: change to private

    public function __construct()
    {
        $this->baseUrl = config('finnotech.base_url');
        $this->clientId = config('finnotech.client_id');
    }

    public function withClientCredentials(): self
    {
        $this->clientCredentialsToken = FinnoTechTokenService::getClientCredentialsToken();
        return $this;
    }

    public function withAuthorizationCode(): self
    {
        /*if(!app()->environment('local')){
            $this->token = config('finnotech.authorization_token');
            return $this;
        }*/
        $this->authorizationToken = FinnoTechTokenService::getAuthorizationToken();
        return $this;
    }

    public function getCardToIban(string $card): array|null
    {
        try {
            $endpoint = "/facility/v2/clients/{$this->clientId}/cardToIban";
            $queryParams = [
                'version' => '2',
                'card' => $card,
            ];

            $response = Http::withToken($this->clientCredentialsToken)
                ->get($this->baseUrl . $endpoint, $queryParams);

            // Method 1: Using Laravel's Log facade
            Log::info('API Response getCardToIban', [
                'url' => $this->baseUrl . $endpoint,
                'status' => $response->status(),
                'body' => $response->json(),
                'headers' => $response->headers()
            ]);

            return $response->json();
        } catch (\Throwable $t) {
            Log::error('FinnoTech CardToIban Error: ' . $t->getMessage());
            throw $t;
        }
    }



    public function verifyMobileOwnership(string $mobile, string $nationalCode): array
    {
        try {
            $endpoint = "/facility/v2/clients/{$this->clientId}/shahkar/verify";

            $queryParams = [
                'mobile' => $mobile,
                'nationalCode' => $nationalCode,
            ];

            $response = Http::withToken($this->clientCredentialsToken)
                ->get($this->baseUrl . $endpoint, $queryParams);

            // Method 1: Using Laravel's Log facade
            Log::info('API Response verifyMobileOwnership', [
                'url' => $this->baseUrl . $endpoint,
                'status' => $response->status(),
                'body' => $response->json(),
                'headers' => $response->headers()
            ]);

            return $response->json();
        } catch (\Throwable $t) {
            Log::error('FinnoTech CardToIban Error: ' . $t->getMessage());
            throw $t;
        }
    }

    public function verifyIbanOwnership(string $iban, string $nationalCode): array
    {
        try {
            $endpoint = "/facility/v2/clients/{$this->clientId}/ibanOwnerVerification";

            $queryParams = [
                'iban' => $iban,
                'nid' => $nationalCode,
            ];

            $response = Http::withToken($this->clientCredentialsToken)
                ->get($this->baseUrl . $endpoint, $queryParams);

            // Method 1: Using Laravel's Log facade
            Log::info('API Response verifyIbanOwnership', [
                'url' => $this->baseUrl . $endpoint,
                'status' => $response->status(),
                'body' => $response->json(),
                'headers' => $response->headers()
            ]);

            return $response->json();
        } catch (\Throwable $t) {
            Log::error('FinnoTech CardToIban Error: ' . $t->getMessage());
            throw $t;
        }
    }

    public function getBanksInfo(?string $trackId = null): array
    {
        try {
            $endpoint = "/facility/v2/clients/{$this->clientId}/banksInfo";
            $queryParams = [];
            if ($trackId) {
                $queryParams['trackId'] = $trackId;
            }

            $response = Http::withToken($this->clientCredentialsToken)
                ->get($this->baseUrl . $endpoint, $queryParams);

            // Method 1: Using Laravel's Log facade
            Log::info('API Response getBanksInfo', [
                'url' => $this->baseUrl . $endpoint,
                'status' => $response->status(),
                'body' => $response->json(),
                'headers' => $response->headers()
            ]);

            return $response->json();
        } catch (\Throwable $t) {
            Log::error('FinnoTech BanksInfo Error: ' . $t->getMessage());
            throw $t;
        }
    }

    public function transferTo(array $queryParams, array $bodyParams): array
    {
        try {
            $endpoint = "/oak/v2/clients/{$this->clientId}/transferTo";

            $url = $this->baseUrl . $endpoint . '?' . http_build_query($queryParams);

            $response = Http::withToken($this->authorizationToken)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, $bodyParams);

            Log::info('API Response transferTo', [
                'url' => $this->baseUrl . $endpoint,
                'status' => $response->status(),
                'body' => $response->json(),
                'headers' => $response->headers()
            ]);

            return $response->json();
        } catch (\Throwable $t) {
            Log::error('FinnoTech TransferTo Error: ' . $t->getMessage());
            throw $t;
        }
    }

    /**
     * Check if the card is active based on API response
     *
     * @param array $data The response data from FinnoTech
     * @return bool
     */
    public function isCardValid(array $data): bool
    {
        return $data['status'] === FinnoTechResponseStatusEnum::DONE->value
            && in_array($data['result']['depositStatus'], ["02", "03"]);
    }

    public function isMobileValid(array $data): bool
    {
        return $data['status'] === FinnoTechResponseStatusEnum::DONE->value && $data['result']['isValid'];
    }

    public function isIbanValid(array $data): bool
    {
        return $data['status'] === FinnoTechResponseStatusEnum::DONE->value && $data['result']['isValid'] === 'yes';
    }
}
