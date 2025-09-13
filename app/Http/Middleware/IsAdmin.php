<?php

namespace App\Http\Middleware;

use App\Enums\Legacy\UserRoleEnum;
use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user() && auth()->user()->role === UserRoleEnum::Admin) {
            return $next($request);
        }

        return response(['message'=>'Unauthorized as an admin.'], 403);
    }
}
