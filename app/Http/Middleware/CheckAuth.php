<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->get('token') || $request->session()->get('token') == '') {
            return redirect('/login');
        }

        try {
            $decode = JWT::decode($request->session()->get('token'), new Key(env('JWT_SECRET'), 'HS256'));
            
            $request->userData = isset($decode->data) ? $decode->data : [];
            if (!str_contains($request->url(), 'authenticate') && empty($decode->data->id)) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 401
                    ], 401);
                }
                return redirect('/login')->with('error', 'Please login to continue.');
            }
            return $next($request);
        }
        
        // EXPIRED
        catch (\Firebase\JWT\ExpiredException $e) {
            return redirect('/login')->with('error', 'Expired access token.');
        } 
        
        // INVALID
        catch (\Firebase\JWT\SignatureInvalidException $e) {
            return redirect('/login')->with('error', 'Access token signature is invalid.');
        }
        catch (\UnexpectedValueException $e) {
            return redirect('/login')->with('error', 'Token signature is invalid.');
        }
    }
}
