<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response|RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        $token = $request->bearerToken();
        if (!$token) {
            return $next($request);
        }
        $decoded = JWT::decode(
            $request->bearerToken(),
            new Key(config('services.jwt_secret_key'), 'HS256')
        );

        $request->setUserResolver(
            static fn() => $decoded->user
        );

        return $next($request);
    }
}
