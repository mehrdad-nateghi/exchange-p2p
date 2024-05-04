<?php

namespace App\Http\Controllers\API\V1\Auth\Signup\SendCode;

use App\Data\API\V1\VerificationCodeData;
use App\Http\Controllers\Controller;
use App\Services\API\V1\EmailNotificationService;
use App\Services\API\V1\VerificationCodeService;
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
            $emailNotificationService->verificationCode($verificationCode, $verificationCodeService->getCode());

            // send to user's email
            return apiResponse()
                ->message(trans('api-message.common.success'))
                ->data($data)
                ->statusCode(Response::HTTP_CREATED)
                ->getApiResponse();
        }catch (\Throwable $t){
            Log::error($t);

            return apiResponse()
                ->failed()
                ->statusCode(Response::HTTP_BAD_REQUEST)
                ->message(trans('api-message.common.error'))
                ->getApiResponse();
        }
    }
}
