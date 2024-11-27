<?php

namespace App\Services\ThirdParty\FinnoTech;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinnoTechService
{
    private string $baseUrl;
    private string $clientId;
    private ?string $token = null;

    public function __construct()
    {
        $this->baseUrl = config('finnotech.base_url');
        $this->clientId = config('finnotech.client_id');
    }

    public function withClientCredentials(): self
    {
        $this->token = FinnoTechTokenService::getClientCredentialsToken();
        return $this;
    }

    public function withAuthorizationCode(): self
    {
        if(!app()->environment('local')){
            $this->token = config('finnotech.authorization_token');
            return $this;
        }
        $this->token = FinnoTechTokenService::getAuthorizationCodeTokenToken();
        return $this;
    }

    public function getCardToIban(string $card): array
    {
        try {
            $endpoint = "/facility/v2/clients/{$this->clientId}/cardToIban";
            $queryParams = [
                'version' => '2',
                'card' => $card,
            ];

            $response = Http::withToken($this->token)
                ->get($this->baseUrl . $endpoint, $queryParams);

            return $response->json();
        } catch (\Throwable $t) {
            Log::error('FinnoTech CardToIban Error: ' . $t->getMessage());
            throw $t;
        }
    }

    public function transferTo(array $queryParams, array $bodyParams): array
    {
        try {
            $endpoint = "/oak/v2/clients/{$this->clientId}/transferTo";

            $url = $this->baseUrl . $endpoint . '?' . http_build_query($queryParams);

            $response = Http::withToken($this->token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, $bodyParams);

            return $response->json();
        } catch (\Throwable $t) {
            Log::error('FinnoTech TransferTo Error: ' . $t->getMessage());
            throw $t;
        }
    }
}
