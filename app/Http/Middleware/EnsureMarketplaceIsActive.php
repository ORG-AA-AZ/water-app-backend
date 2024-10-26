<?php

namespace App\Http\Middleware;

use Closure;

class EnsureMarketplaceIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (! $request->user()->is_active) {
            return response()->json(['message' => __('messages.inactive_marketplace')], 403);
        }

        return $next($request);
    }
}
