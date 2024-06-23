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

            $code = $verificationCodeService->generateCode();
            $encryptCode = $verificationCodeService->encryptCode($code);

            $data = [
                'to' => $validated['to'],
                'via' => $validated['via'],
                'type' => $validated['type'],
                'code' => $encryptCode,
            ];

            $verificationCode = $verificationCodeService->store($data);

            // send code via email
            $emailNotificationService->verificationCode($verificationCode,$code);

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.verification_code_sent_successfully'))
                ->created()
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
