<?php

namespace App\Http\Controllers\API\V1\ThirdParty\FinnoTech\User;

use App\Enums\FinnoTechResponseStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\ThirdParty\User\VerifyMobileOwnershipRequest;
use App\Services\ThirdParty\FinnoTech\FinnoTechService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerifyMobileOwnershipController extends Controller
{
    public function __invoke(
        VerifyMobileOwnershipRequest $request,
        FinnoTechService  $finnoTechService
    ): JsonResponse
    {
        try {
            $mobile = $request->input('mobile');
            $nationalCode = $request->input('national_code');

            $data = $finnoTechService->withClientCredentials()->verifyMobileOwnership($mobile, $nationalCode);

            if ($data['status'] === FinnoTechResponseStatusEnum::DONE->value && $data['result']['isValid']) {
                $user = Auth::user();

                $user->update([
                    'national_code' => $nationalCode,
                    'mobile_ownership_verified_at' => now()
                ]);

                return apiResponse()
                    ->message(trans('api-messages.mobile_ownership_verified'))
                    ->getApiResponse();
            }

            return apiResponse()
                ->failed()
                ->badRequest()
                ->message(trans('api-messages.mobile_ownership_invalid'))
                ->getApiResponse();

        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
