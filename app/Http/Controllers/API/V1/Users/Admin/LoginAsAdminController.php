<?php

namespace App\Http\Controllers\API\V1\Users\Admin;

use App\Enums\RoleNameEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Facades\App\Services\Global\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginAsAdminController extends Controller
{
    public function __invoke(
        Request $request,
        User $user
    ) {
        try {
            if ($adminId = $request->session()->get('admin_id')) {
                $admin = User::findOrFail($adminId);

                if (!$admin->hasRole(RoleNameEnum::ADMIN->value)) {
                    HttpResponse::unauthorized();
                }

                config(['session.cookie' => 'admin_session']); // todo: use const for value
                Auth::guard('web')->login($admin);
                $request->session()->regenerate();
                $request->session()->forget('admin_id');

                return apiResponse()
                    ->message(trans('api-messages.admin_return_success'))
                    ->getApiResponse();
            }

            return apiResponse()
                ->message(trans('api-messages.admin_return_failed'))
                ->failed()
                ->badRequest()
                ->getApiResponse();
        } catch (\Throwable $t) {
            HttpResponse::serverError();
        }
    }
}
