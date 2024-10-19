<?php

namespace App\Http\Controllers\API\V1\Users\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class LoginAsUserController extends Controller
{
    public function __invoke(
        Request $request,
        User $user
    ) {
        try {
            Auth::guard('api')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Auth::guard('api')->login($user);

            return Redirect::away(config('constants.user_dashboard_url'));
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
