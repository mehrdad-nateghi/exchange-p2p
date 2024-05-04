<?php

namespace App\Http\Controllers\API\V1\Auth\Signup;

use App\Data\API\V1\VerificationCodeData;
use App\Enums\API\V1\VerificationCodeTypeEnum;
use App\Enums\API\V1\VerificationCodeViaEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\Signup\SignupRequest;
use App\Services\API\V1\EmailNotificationService;
use App\Services\API\V1\VerificationCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SignupController extends Controller
{
    public function __invoke(
        SignupRequest $request,
        VerificationCodeService $verificationCodeService,
        EmailNotificationService $emailNotificationService
    ): JsonResponse {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $verificationCodeData = VerificationCodeData::from([
                    'to' => $validated['email'],
                    'via' => VerificationCodeViaEnum::EMAIL->value,
                    'type' => VerificationCodeTypeEnum::SET_PASSWORD->value
                ]
            );

            $verificationCode = $verificationCodeService->store($verificationCodeData);

            // send code via email
            $emailNotificationService->verificationCode($verificationCode,$verificationCodeService->getCode());

            DB::commit();

            //todo-mn: new message: verification code sent successfully to the email
            return responseService()
                ->setMessage(trans('api-message.common.success'))
                //->setData($verificationCode)
                ->setStatusCode(Response::HTTP_CREATED)
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();

            Log::error($t);

            return responseService()
                ->setStatus('failed')
                ->setStatusCode(Response::HTTP_BAD_REQUEST)
                ->setMessage(trans('api-message.common.error'))
                ->getApiResponse();
        }
    }
}
