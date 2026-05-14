<?php 

namespace Amreljako\SecureCore\Http\Middleware;

use Closure;
use Amreljako\SecureCore\Security\SecureId;

class BolaProtection
{
    public function handle($request, Closure $next)
    {
        $secureId = new SecureId();
        $params = $request->route()->parameters();

        foreach ($params as $key => $value) {
            if (is_string($value) && !is_numeric($value)) {
                $decoded = $secureId->decode($value);
                if ($decoded) {
                    $request->route()->setParameter($key, $decoded);
                }
            }
        }

        return $next($request);
    }
}