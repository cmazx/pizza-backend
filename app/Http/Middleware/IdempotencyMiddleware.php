<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class IdempotencyMiddleware
{
    const IDEMPOTENCY_HEADER = "Idempotency-Key";
    const EXPIRATION_IN_MINUTES = 60 * 15;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->method() == 'GET' || $request->method() == 'DELETE') {
            return $next($request);
        }

        $requestId = $request->header(self::IDEMPOTENCY_HEADER);
        if (!$requestId) {
            return response()->json(['message' => 'Idempotency-header required', 'code' => 9], 400);
        }

        if (Cache::has($requestId)) {
           return Cache::get($requestId);
        }


        $cacheKey = $request->header(self::IDEMPOTENCY_HEADER);
        $response = $next($request);
        if ($response->getStatusCode() > 299) {
            return $response;
        }

        if (!Cache::has($cacheKey)) {
            Cache::put($cacheKey, $response, self::EXPIRATION_IN_MINUTES);
        }

        return $response;
    }

}
