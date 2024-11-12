<?php

namespace App\Http\Controllers\API\V1\Users\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class LoginAsAdminController extends Controller
{
    public function __invoke(
        Request $request,
        User $user
    ) {
        try {
            if ($adminId = $request->session()->get('admin_id')) {
                $admin = User::findOrFail($adminId);
                Auth::login($admin);
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
            Log::error($t);
            return internalServerError();
        }
    }
}
