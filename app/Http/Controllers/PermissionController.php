<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function checkAdminPermissions($user_id){

        try{
            $record=DB::select("SELECT * FROM users WHERE id=? AND Permissions_id=3",[
            $user_id
            ]);
            if($record==[]){
                return response()->json([
                    'res'=>false,
                    'msg'=>'The user is not  an admin'
                ],200);
            }
            return response()->json([
                'res'=>true,
                'msg'=>'The user have permissions of admin',
                'data'=>$record
            ],200);
        }catch(\Throwable $th) {
            return response()->json([
                'res' => false,
                'msg' => 'Error'
            ], 500);
        }
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
