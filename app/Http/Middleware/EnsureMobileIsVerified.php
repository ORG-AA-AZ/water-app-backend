<?php

namespace App\Http\Middleware;

use Closure;

class EnsureMobileIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (! $request->user()->mobile_verified_at) {
            return response()->json(['message' => __('messages.mobile_not_verified')], 403);
        }

        return $next($request);
    }
}
