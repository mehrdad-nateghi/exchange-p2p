<?php

namespace App\Http\Controllers\API\V1\ThirdParty\FinnoTech\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\ThirdParty\User\CardToIbanRequest;
use App\Http\Resources\CardToIbanErrorResource;
use App\Http\Resources\CardToIbanSuccessResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CardToIbanController extends Controller
{
    public function __invoke(
        CardToIbanRequest $request
    ): JsonResponse
    {
        $clientId = config('finnotech.client_id');
        $token = config('finnotech.token');
        $baseUrl = config('finnotech.base_url');
        $endpoint = "/facility/v2/clients/{$clientId}/cardToIban";

        $card = $request->input('card');

        $queryParams = [
            'version' => '2',
            'card' => $card,
        ];

        try {
            $response = Http::withToken($token)
                ->get($baseUrl . $endpoint, $queryParams);

            $data = $response->json();

            if ($response->successful()) {
                $resource =  new CardToIbanSuccessResource($data);

                return apiResponse()
                    ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.card_to_iban')]))
                    ->data($resource)
                    ->getApiResponse();
            } else {
                $resource =  new CardToIbanErrorResource($data);

                return apiResponse()
                    ->failed()
                    ->message(trans('api-messages.request_failed', ['attribute' => trans('api-messages.card_to_iban')]))
                    ->data($resource)
                    ->getApiResponse();
            }
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
