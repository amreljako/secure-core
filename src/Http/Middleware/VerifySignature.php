<?php

namespace Amreljako\SecureCore\Http\Middleware;

use Closure;

class VerifySignature
{
public function handle($request, Closure $next)
{
    if (! config('secure-core.api.signature_check', true)) {
        return $next($request);
    }

    $signature = $request->header('X-Secure-Signature');
    
    if (! $signature) {
        return response()->json(['error' => 'Security Signature is missing'], 403);
    }

    $data = $request->all();
    ksort($data); 
    $payload = json_encode($data);
    
    // I Use APP_KEY as a Private Key
    $secret = config('app.key'); 

    $computedSignature = hash_hmac('sha256', $payload, $secret);

    if (! hash_equals($computedSignature, (string) $signature)) {
        return response()->json(['error' => 'Invalid Security Signature'], 403);
    }

    return $next($request);
}
}