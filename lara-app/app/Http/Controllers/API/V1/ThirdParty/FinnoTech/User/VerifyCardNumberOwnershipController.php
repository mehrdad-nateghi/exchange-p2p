<?php

namespace App\Http\Controllers\API\V1\ThirdParty\FinnoTech\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\ThirdParty\User\VerifyCardNumberOwnershipRequest;
use App\Services\ThirdParty\FinnoTech\FinnoTechService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerifyCardNumberOwnershipController extends Controller
{
    public function __invoke(
        VerifyCardNumberOwnershipRequest $request,
        FinnoTechService                 $finnoTechService
    ): JsonResponse
    {
        try {
            $user = Auth::user();
            $nationalCode = $user->national_code;

            $cardNumber = $request->input('card_number');
            $cardToIbanData = $finnoTechService->withClientCredentials()->getCardToIban($cardNumber);

            Log::info('Resp VerifyCardNumberOwnershipController', [
                'cardToIbanData' => $cardToIbanData,
                'cardNumber' => $cardNumber,
                'nationalCode' => $nationalCode,
            ]);

            if ($finnoTechService->isCardValid($cardToIbanData)) {
                $verifyIbanOwnershipData = $finnoTechService->withClientCredentials()->verifyIbanOwnership($cardToIbanData['result']['IBAN'], $nationalCode);

                if($finnoTechService->isIbanValid($verifyIbanOwnershipData)) {
                    return apiResponse()
                        ->message(trans('api-messages.card_number_ownership_verified'))
                        ->getApiResponse();
                }else{
                    return apiResponse()
                        ->unProcessableEntity()
                        ->message(trans('api-messages.card_number_ownership_invalid'))
                        ->getApiResponse();
                }
            }

            return apiResponse()
                ->unProcessableEntity()
                ->message(trans('api-messages.card_number_is_inactive'))
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
