<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CheckAuthFrontend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->bearerToken() || $request->bearerToken() == '') {
            return response()->json([
                'message' => 'Access token not provided.',
                'success' => false,
            ], 401);
        }


        try {
            $decode = JWT::decode($request->bearerToken(), new Key(env('JWT_SECRET'), 'HS256'));
            
            $request->userData = isset($decode->data) ? $decode->data : [];
            if (!str_contains($request->url(), 'login') && empty($decode->data->id)) {
                return response()->json([
                    'message' => 'Please login to continue.',
                    'success' => false,
                ], 401);
            }
            return $next($request);
        }
        
        // EXPIRED
        catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json([
                'message' => 'Expired access token.',
                'success' => false,
            ], 401);
        } 
        
        // INVALID
        catch (\Firebase\JWT\SignatureInvalidException $e) {
            return response()->json([
                'message' => 'Access token signature is invalid.',
                'success' => false,
            ], 401);
        }
        catch (\UnexpectedValueException $e) {
            return response()->json([
                'message' => 'Token signature is invalid.',
                'success' => false,
            ], 401);
        }
    }
}
