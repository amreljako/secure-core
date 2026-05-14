<?php

namespace Amreljako\SecureCore\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $headers = config('secure-core.headers', []);

        foreach ($headers as $header => $value) {
            $response->headers->set($header, $value);
        }

        return $response;
    }
}