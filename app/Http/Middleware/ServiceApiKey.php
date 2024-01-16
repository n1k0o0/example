<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->headers->get('api-key') || $request->headers->get('api-key') !== config(
                'services.internal_api_key'
            )) {
            throw new AuthenticationException('К сожалению, вы не авторизованы для доступа к этим данным.');
        }
        return $next($request);
    }
}
