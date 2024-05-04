<?php

namespace App\Http\Controllers\V1\Auth\Signup\SendCode;

use App\Data\VerificationCodeData;
use App\Http\Controllers\Controller;
use App\Services\EmailNotificationService;
use App\Services\VerificationCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SendCodeController extends Controller
{
    public function __invoke(
        VerificationCodeData $data,
        VerificationCodeService $verificationCodeService,
        EmailNotificationService $emailNotificationService,
    ): JsonResponse {
        try{
            $verificationCode = $verificationCodeService->store($data->toArray());

            // send code via email
            // need a service
            $emailNotificationService->sendVerificationCode($verificationCode, $verificationCodeService->getCode());

            // send to user's email
            return responseService()
                ->setMessage(trans('api-message.common.success'))
                ->setData($data)
                ->setStatusCode(Response::HTTP_CREATED)
                ->getApiResponse();
        }catch (\Throwable $t){
            Log::error($t);

            return responseService()
                ->setStatus('failed')
                ->setStatusCode(Response::HTTP_BAD_REQUEST)
                ->setMessage(trans('api-message.common.error'))
                ->getApiResponse();
        }
    }
}
