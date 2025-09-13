<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\LoginRequest;
use App\Models\User;
use App\Services\API\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class LogOutController extends Controller
{
    public function __invoke(
        Request $request,
        UserService $userService,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            /**
             * @var User $user
             */
            $user = Auth::user();
            $userService->logout($request,$user);

            DB::commit();

            return apiResponse()
                ->success()
                ->message(trans('api-messages.logout_successful'))
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
