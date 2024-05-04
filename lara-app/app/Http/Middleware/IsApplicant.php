<?php

namespace App\Http\Middleware;

use App\Enums\old\UserRoleEnum;
use Closure;
use Illuminate\Http\Request;

class IsApplicant
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
        if (auth()->user() && auth()->user()->role === UserRoleEnum::Applicant) {
            return $next($request);
        }

        return response(['message'=>'Unauthorized as an applicant.'], 403);
    }
}
