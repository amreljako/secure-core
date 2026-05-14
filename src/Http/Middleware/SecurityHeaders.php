<?php

namespace Amreljako\SecureCore\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('X-Powered-By', ''); 
            $response->header('Server', '');
        }

        if ($response instanceof Response) {
            $headers = config('secure-core.headers', []);

            foreach ($headers as $header => $value) {
                $response->headers->set($header, $value);
            }
        }

        return $response;
    }
}