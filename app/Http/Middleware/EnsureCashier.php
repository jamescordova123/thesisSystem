<?php

namespace App\Http\Middleware;

use App\Enums\AppRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCashier
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasAnyRole([
            AppRole::Cashier->value,
            AppRole::SuperAdmin->value,
        ])) {
            abort(403);
        }

        return $next($request);
    }
}
