<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Data\VerificationCodeData;
use App\Enums\VerificationCodeTypeEnum;
use App\Enums\VerificationCodeViaEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\ResendCodeRequest;
use App\Services\API\V1\EmailNotificationService;
use App\Services\API\V1\VerificationCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResendCodeController extends Controller
{
    public function __invoke(
        ResendCodeRequest $request,
        VerificationCodeService $verificationCodeService,
        EmailNotificationService $emailNotificationService
    ): JsonResponse {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $verificationCodeData = VerificationCodeData::from([
                    'to' => $validated['to'],
                    'via' => $validated['via'],
                    'type' => $validated['type']
                ]
            );

            $verificationCode = $verificationCodeService->store($verificationCodeData);

            // send code via email
            $emailNotificationService->verificationCode($verificationCode,$verificationCodeService->getCode());

            DB::commit();

            return apiResponse()
                ->message(trans('api-message.verification_code_sent_successfully'))
                ->created()
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
