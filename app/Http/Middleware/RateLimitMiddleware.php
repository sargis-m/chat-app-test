<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->limiter->tooManyAttempts($this->resolveRequestKey($request), 10)) {
            return response()->json(['message' => 'Too Many Requests'], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $this->limiter->hit($this->resolveRequestKey($request), 1);

        return $next($request);
    }

    protected function resolveRequestKey(Request $request): string
    {
        return sha1($request->method() . '|' . $request->ip());
    }
}
