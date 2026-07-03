<?php

namespace App\Http\Middleware;

use App\Enums\AppRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrar
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasAnyRole([
            AppRole::Registrar->value,
            AppRole::SuperAdmin->value,
        ])) {
            abort(403);
        }

        return $next($request);
    }
}
