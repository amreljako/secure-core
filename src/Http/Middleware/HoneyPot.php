<?php

namespace Amreljako\SecureCore\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HoneyPot
{
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();

        if (Cache::has("secure_core_blocked_{$ip}")) {
            abort(403, 'Access denied due to suspicious activity.');
        }

        $path = $request->path();
        $traps = config('secure-core.honeypot.traps', []);

        if (in_array($path, $traps)) {
            return $this->handleSuspiciousAttempt($request);
        }

        $response = $next($request);

        if ($response->getStatusCode() === 404) {
            $this->incrementSuspicionScore($ip);
        }

        return $response;
    }

    protected function handleSuspiciousAttempt($request)
    {
        $ip = $request->ip();
        $this->incrementSuspicionScore($ip, 5); 

        Log::warning("Security Alert: Honeypot trap triggered", [
            'ip' => $ip,
            'path' => $request->path(),
            'user_agent' => $request->userAgent()
        ]);

        abort(404);
    }

    protected function incrementSuspicionScore($ip, $amount = 1)
    {
        $key = "suspicion_score_{$ip}";
        $score = Cache::get($key, 0) + $amount;
        
        Cache::put($key, $score, now()->addHour());

        if ($score >= config('secure-core.honeypot.threshold', 20)) {
            Cache::put("secure_core_blocked_{$ip}", true, now()->addDays(1));
            Log::emergency("IP {$ip} has been blacklisted after multiple security triggers.");
        }
    }
}