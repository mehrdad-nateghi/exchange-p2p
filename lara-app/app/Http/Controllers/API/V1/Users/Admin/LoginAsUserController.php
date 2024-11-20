<?php

namespace App\Http\Controllers\API\V1\Users\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginAsUserController extends Controller
{
    public function __invoke(
        Request $request,
        User $user
    ) {
        try {
            $request->session()->put('admin_id', Auth::id());

            Auth::guard('web')->login($user);

            return apiResponse()
                ->message(trans('api-messages.impersonation_success', ['attribute' => trans('api-messages.user')]))
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
