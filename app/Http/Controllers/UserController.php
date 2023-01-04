<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserExistRequest;

class UserController extends Controller
{

    public function index()
    {
        //
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
    public function show(UserExistRequest $request)
    {
        try{
            $resultado = DB::select('call sp_if_exists_user(?)',[
                $request->mail,
            ]);
            if(json_decode(json_encode($resultado['0']), true)["`@exists_email`"]==0){
                return response()->json([
                    'res' =>false,
                    'error' => 'The user does not exist'
                                            ], 404);
            }else{
                return response()->json([
                    "res" => true,
                    "data" => 'The user exist'
                    ], 200);
            }
        }catch(\Exception $e){
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage(),
            ], 404);
        }
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
