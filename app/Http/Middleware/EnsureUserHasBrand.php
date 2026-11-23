<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasBrand
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->brand) {
            return redirect()->route('seller.create');
        }

        return $next($request);
    }
}