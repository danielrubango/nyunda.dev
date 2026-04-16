<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetRobotsTag
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $robots = 'noindex,follow'): Response
    {
        $response = $next($request);
        $response->headers->set('X-Robots-Tag', $robots);

        return $response;
    }
}
