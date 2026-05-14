<?php

namespace Amreljako\SecureCore\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class HoneyPot
{
    public function handle($request, Closure $next)
    {
        $traps = config('secure-core.honeypot.traps', []);
        $path = $request->path();

        if (in_array($path, $traps)) {
            $ip = $request->ip();
            
            Log::warning("HoneyPot Triggered: IP {$ip} tried to access sensitive path: {$path}");

            if (config('secure-core.honeypot.auto_block')) {
                Cache::put("blocked_ip_{$ip}", true, now()->addHour());
            }

            abort(404);
        }

        if (Cache::has("blocked_ip_" . $request->ip())) {
            abort(403, 'Your IP is temporarily flagged for suspicious activity.');
        }

        return $next($request);
    }
}