<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsWhenConfigured
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $appUrl = (string) config('app.url');

        if (
            ! app()->runningUnitTests()
            && str_starts_with($appUrl, 'https://')
            && ! $request->isSecure()
        ) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
