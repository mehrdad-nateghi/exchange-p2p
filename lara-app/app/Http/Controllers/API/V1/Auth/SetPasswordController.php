<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\SetPasswordRequest;
use App\Services\API\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetPasswordController extends Controller
{
    public function __invoke(
        SetPasswordRequest $request,
        UserService $userService
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            $password = $validatedData['password'];
            $user = Auth::user();

            $userService->setPassword($user, $password);

            $data = [
                'user' =>  $userService->createResource($user),
            ];

            DB::commit();

            return apiResponse()
                ->message(trans('api-message.password_set_successfully'))
                ->data($data)
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}