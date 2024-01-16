<?php

namespace App\Http\Middleware;

use App\Enums\Auth\UserTypeEnum;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthJWT
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $role
     * @return Response|RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next, string $role): Response|RedirectResponse|JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $decoded = JWT::decode(
            $request->bearerToken(),
            new Key(config('services.jwt_secret_key'), 'HS256')
        );

        if (!in_array($role, UserTypeEnum::values(), true)) {
            return response()->json(['error' => 'Неправильная валидация роли'], 403);
        }

        if ($role === UserTypeEnum::ADMIN->value && !in_array(
                $decoded->user->type,
                [UserTypeEnum::ADMIN->value, UserTypeEnum::SUPER_ADMIN->value],
                true
            )) {
            return response()->json(['error' => 'Unauthenticated.ADMIN'], 403);
        }
        if ($role === UserTypeEnum::SUPER_ADMIN->value && $decoded->user->type !== UserTypeEnum::SUPER_ADMIN->value) {
            return response()->json(['error' => 'Unauthenticated.SUPER_ADMIN'], 403);
        }

        if ($role === UserTypeEnum::JURY->value && $decoded->user->type !== UserTypeEnum::JURY->value) {
            return response()->json(['error' => 'Unauthenticated.JURY'], 403);
        }

        if ($role === UserTypeEnum::USER->value) {
            if ($decoded->user->type !== UserTypeEnum::USER->value) {
                return response()->json(['error' => 'Unauthenticated.USER'], 403);
            }
            if (!$decoded->user->school_id) {
                return response()->json(['error' => 'Сначала создайте школу'], 403);
            }
        }

        $request->setUserResolver(
            static fn() => $decoded->user
        );

        return $next($request);
    }
}
