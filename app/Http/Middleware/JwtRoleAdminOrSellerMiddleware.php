<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtRoleAdminOrSellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = JWTAuth::getToken();
            
            if(JWTAuth::decode($token)->get('role')=="Admin" || JWTAuth::decode($token)->get('role')=="vendedor"){
                return $next($request);
            }
            
            return response()->json(['msg'=> 'permissions denied, you need to be admin or seller'],401);
            
        } catch (Exception $e) {
            if($e instanceof TokenInvalidException){
                return response()->json(['msg'=> 'Invalid Token'],401);
            }
            if($e instanceof TokenExpiredException){
                return response()->json(['msg'=> 'expired token'],401);
            }
            return response()->json(['msg'=> 'permissions denied'],401);
        }
        return $next($request);
    }
}
