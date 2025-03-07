<?php

namespace App\Http\Controllers\API\V1\ThirdParty\FinnoTech\User;

use App\Enums\FinnoTechResponseStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\ThirdParty\User\CardToIbanRequest;
use App\Http\Resources\CardToIbanSuccessResource;
use App\Services\ThirdParty\FinnoTech\FinnoTechService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CardToIbanController extends Controller
{
    public function __invoke(
        CardToIbanRequest $request,
        FinnoTechService  $finnoTechService
    ): JsonResponse
    {
        try {
            $data = $finnoTechService->withClientCredentials()->getCardToIban($request->input('card'));

            if ($finnoTechService->isCardValid($data)) {
                return apiResponse()
                    ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.card_to_iban')]))
                    ->data(new CardToIbanSuccessResource($data))
                    ->getApiResponse();
            }

            return apiResponse()
                ->failed()
                ->badRequest()
                ->message(trans('api-messages.request_failed', ['attribute' => trans('api-messages.card_to_iban')]))
                ->getApiResponse();

        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
