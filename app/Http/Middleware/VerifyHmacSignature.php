<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHmacSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.hmac.key');
        $signature = $request->header('X-Signature');

        if (!$signature) {
            return response()->json(['message' => 'Signature missing'], 400);
        }

        $computed = hash_hmac('sha256', $request->getContent(), $secret);

        if (!hash_equals($computed, $signature)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        return $next($request);
    }
}
