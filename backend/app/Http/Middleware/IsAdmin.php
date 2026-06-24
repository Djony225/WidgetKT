<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        if(!$request->user() || $request->user()->role !=='admin'){
            return response()->json([
                'message' => "access denied"
            ], 403);
        }
        return $next($request);
    }
}