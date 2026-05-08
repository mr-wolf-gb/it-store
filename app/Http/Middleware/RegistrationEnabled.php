<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('settings.registration_enabled', true)) {
            abort(403, __('User registration is currently disabled.'));
        }

        return $next($request);
    }
}
