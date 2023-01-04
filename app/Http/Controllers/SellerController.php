<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SellersendCommentRequest;

class SellerController extends Controller
{
    public function detailsSeller($seller_id)
    {
        try{
           // $seller_datails = DB::select('select * from users where id=(?)',[
            $seller_datails = DB::select('CALL sp_list_user_seller_specific(?)',[
                $seller_id
            ]);   

            $data = json_encode([
                $seller_datails
                            ]);
            $data2 = strval($data);

            if($data2=="[[{\"exit\":0}]]"){
                return response()->json([
                    'res' =>false,
                    'error' => 'Seller does not exist'
                                            ], 404);
            }  
                return response()->json([
                    "res" => true,
                    "data" => $seller_datails,
                    ], 200);
            
        }catch(\Exception $e){
            return response()->json([
                'res' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
        
    }

    public function sendValuation(SellersendCommentRequest $request ,$seller_id)
    {
        try{
            $users = DB::select('select * from users where id=(?)',[
                $seller_id
            ]); 

             if($users==[]){
                return response()->json([
                    'res' =>false,
                    'error' => 'Seller does not exist'
                                            ], 404);
             }else{
                $iduser = DB::select('select users_id from valuations where users_id=(?)',[
                    $seller_id
                ]);  

                if($iduser!=[]){
                    return response()->json([
                        'res' =>false,
                        'error' => 'Error, existing comment'
                                                ], 400);
                }else{
                    $comments = DB::statement('CALL sp_add_valuations(?,?,?,?)',[
                        $request->stars,
                        $request->description,
                        $request->products_id,
                        $request->users_id=$seller_id,
                     ]);
                        return response()->json([
                            'res' => true,
                            'msg' => 'Comment added',
                        ], 201); 
                    }
                }    
            }catch(\Throwable  $th){
                return response()->json([
                    'res' => false,
                    'error' => $th->getMessage(),
                ], 500);
            }         
    }

    public function oneValuation($valuation_id){
        try{
            $valuation = DB::select('select * from valuations where id=(?)',[
                $valuation_id
            ]);

            if($valuation==[]){
                return response()->json([
                    'res' =>false,
                    'error' => 'Are no comment'
                                            ], 400);
            }else{
                    return response()->json([
                        'res' => true,
                        'data' => $valuation,
                    ], 200); 
                }

        }catch(\Throwable  $th){
                return response()->json([
                    'res' => false,
                    'error' => $th->getMessage(),
                ], 500);
            } 
    }

    public function allValuation()
    {
        try{
            $valuations = DB::select('select * from valuations',[
                
            ]);  
            if($valuations==[]){
                return response()->json([
                    'res' =>false,
                    'error' => 'There are no comments'
                                            ], 404);
            }
                return response()->json([
                    "res" => true,
                    "data" => $valuations,
                    ], 200);
            
        }catch(\Throwable $e){
            return response()->json([
                'res' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}