<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            $accessToken = $request->bearerToken();
            if ( empty( $accessToken ) || $accessToken != auth()->user()->currentAccessToken()) {
                return response()->json(['message' => 'Bearer token missing'], 401);
            }
            return response()->json(['message' => 'Bearer token missing'], 401);
    }
}
