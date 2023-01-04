<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

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
class ChangePermissionsController extends Controller
{
    public function buyerToSeller(Request $request)
    {
        $permiso=2;
        try {
            $user=JWTAuth::parseToken()->authenticate();

            $credentials = [
                "email"=>$user->email,
                "password"=>$request->password
            ];

            DB::select('CALL sp_update_user_permission(?,?)', [
                $user->id,
                $permiso,
            ]);

             if (!$token = auth()->claims(['role' => role_user($permiso)])->attempt($credentials)) {
                return response()->json([
                    'res' => false,
                    'msg' => "invalid credentials",
                ], 404);
            } 
            return response()->json([
                'res' => true,
                'token' => $token,
                'newrole'=>role_user($permiso)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    public function sellerToBuyer(Request $request)
    {
        $permiso=1;
        try {
            $user=JWTAuth::parseToken()->authenticate();

            $credentials = [
                "email"=>$user->email,
                "password"=>$request->password
            ];
            DB::select('CALL sp_update_user_permission(?,?)', [
                $user->id,
                $permiso,
            ]);
             if (!$token = auth()->claims(['role' => role_user($permiso)])->attempt($credentials)) {
                return response()->json([
                    'res' => false,
                    'msg' => "invalid credentials",
                ], 404);
            } 
            return response()->json([
                'res' => true,
                'token' => $token,
                'newrole'=>role_user($permiso)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }
}
