<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ForceJson
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse|Response|RedirectResponse
    {
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
