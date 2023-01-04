<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\TokenAccessRequest;
use App\Http\Requests\TokenRegisterRequest;
use Illuminate\Validation\ValidationException;


function rand_user($valor)
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $number = "0123456789";
    $pre = substr(str_shuffle($chars), 0, 1);
    $suf = substr(str_shuffle($number), 0, 4);

    if (strlen($valor) == 0 || $valor == null) {
        return $pre . "user" . $suf;
    } else {
        return $valor;
    }
}
function role_user($valor)
{
    switch ($valor) {
        case 1:
            return "Comprador";
        case 2:
            return "vendedor";
        case 3:
            return "Admin";
        default:
            return "Comprador";
    }
}
class TokenAutenticarController extends Controller
{

    public function singup(TokenRegisterRequest $request)
    {
        try {
            DB::select('CALL sp_add_users(?,?,?,?,?,?,?,?,?)', [
                $request->firstname,
                $request->lastname,
                rand_user($request->username),
                $request->email,
                $request->telephone,
                $request->direction,
                $request->document,
                $request->type_document,
                bcrypt($request->password),
            ]);
            $user = User::where('email', $request->email)->first();
            $token = auth()->claims(['role' => role_user($user->permissions_id)])->login($user);
            $userData =["username"=>$user->username,"email"=>$user->email, "role"=>role_user($user->permissions_id) ];
            return response()->json([
                'res' => true,
                'msg' => 'successfully registered user',
                'token' => $token, 
                'userData'=>$userData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage(),

            ], 500);
        }
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(TokenAccessRequest $request)
    {
        try {

            $credentials = $request->only('email', 'password');
            $user = User::where('email', $request->email)->first();
            if (!$token = auth()->claims(['role' => role_user($user->permissions_id)])->attempt($credentials)) {
                return response()->json([
                    'res' => false,
                    'msg' => "invalid credentials"
                ], 404);
            }
            $userData =["username"=>$user->username,"email"=>$user->email, "role"=>role_user($user->permissions_id) ];
            return response()->json([
                'res' => true,
                'token' => $token,
                'userData'=>$userData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        try {
            
            $refreshToken = $this->respondWithToken(auth()->refresh());

            return response()->json([
                'res' => true,
                'New Token' => $refreshToken
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            auth()->logout();
            return response()->json([
                'res' => true,
                'msg' => 'Token Removed',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
