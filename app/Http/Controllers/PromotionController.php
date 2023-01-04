<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\PromotionAddRequest;
use App\Http\Requests\PromotionExistRequest;
use App\Http\Requests\PromotionBySellerExistRequest;
use App\Http\Requests\PromotionStateUpdateRequest;
use App\Http\Requests\PromotionUpdateRequest;
use Illuminate\Support\Facades\Log;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Promotion=DB::select("select
                                code,
                                type,
                                date,
                                end_date,
                                start_date,
                                percentage
                            from promotions order by percentage;");
        $collection=collect($Promotion);
        return response()->json(
            [
                'res'=>true,
                'data'=>$collection
            ],200);
    }

    public function listPromotionBySeller(PromotionBySellerExistRequest $request)
    {
        $Promotion=DB::select("call sp_list_promotions_by_seller(?);",[
            $request->users_id,
        ]);
        $collection=collect($Promotion);
        return response()->json(
            [
                'res'=>true,
                'data'=>$collection
            ],200);
    }

    public function addPromotion(PromotionAddRequest $request)
    {            
        try{
            $coupons_code = DB::select('select code from coupons where code=(?)',[
                $request->code,
            ]);
            DB::insert('CALL sp_add_promotions(?,?,?,?,?,?)',[
                $request->code,
                $request->type,
                $request->start_date,
                $request->end_date,
                $request->percentage,
                $request->users_id]);

            return response()->json([
                "code"    => 201,
                "status"  => "Created",
                "message" => "The request succeeded, and create new promotion"    
            ],201);

        } catch (\Throwable $th) {
            return response()->json([
                "code"    =>  500,
                "status" =>  "Internal Server Error",
                "message" => "The server has encountered a situation it does not know how to handle.",
                "error"   =>  $th    
            ],500);
        };
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
    public function existPromotion($promotion_id)
    {
        
        try{

            $promotion_datails = DB::select('CALL sp_if_exists_promotion(?)',[
                $promotion_id
            ]);   

            $data = json_encode([
                $promotion_datails
                            ]);
            $data2 = strval($data);

            if($data2=="[[{\"exit\":0}]]"){
                return response()->json([
                    'res' =>false,
                    'error' => 'Promotion does not exist'
                                            ], 404);
            }  
            
            $promotion = DB::select('select * from promotions where id=(?)',[
                $promotion_id
            ]);
            return response()->json([
                "res" => true,
                "msg" => 'The promotion exist',
                "data" => $promotion,
                ], 200);
                    
        }catch(\Exception $e){
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }
    public function show (PromotionExistRequest $request)
    {
        try{
            $resultado = DB::select('select * from promotions where code = (?)',[
                $request->code,
            ]);
    
            if($resultado==[]){
                return response()->json([
                    'res' =>false,
                    'error' => 'The promotion does not exist'
                                            ], 500); 
            }else{
                return response()->json([
                    "res" => true,
                    "data" => $resultado,
                    ], 200);
            }
        }catch(\Exception $e){
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PromotionUpdateRequest $request, $promotion_id)
    {
        try {
            $promotions = DB::select('select * from promotions where id = ?', [
                $promotion_id
            ]);

            if($promotions == []){
                return response()->json([
                    'res' =>false,
                    'error' => 'The promotion does not exist'
                    ], 404); 
            }else{
                $promotions_up = DB::select('select * from promotions where id = ? && is_actived = "1"',[
                    $promotion_id
                ]);

                if($promotions_up == [])
                {
                    return response()->json([
                        'res' =>false,
                        'error' => 'The promotion does not exist'
                        ], 404); 
                }
                else{
                    DB::update('call sp_update_promotion (?, ?, ?, ?, ?, ?)', [
                        $promotion_id,
                        $request -> code,
                        $request -> type,
                        $request -> start_date,
                        $request -> end_date,
                        $request -> percentage
                     ]);
                     return response()->json([
                        'res' => true,
                        'msg' => 'Promotion modified'
                     ], 201);
                }   
            }
           
        } catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => 'Error modifying promotion'
            ], 500);
        }
    }

        /**
     * Update State Promotion the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function updateStatePromotion (PromotionStateUpdateRequest $request, $promotion_id)
    {
        try {
            $promotions = DB::update('call sp_update_is_actived_promotion (?, ?)',[
                $promotion_id,
                $request -> is_actived
            ]);

            Log::info($promotions);
            if ($promotions)
            {
                $promotions_st = DB::select('select * from promotions where id = ? && is_actived = "1"',[
                    $promotion_id
                ]);

                if($promotions_st == [])
                {
                    return response()->json([
                        'res' => true,
                        'msg' => 'Promotion is deactivated'
                    ], 201);
                }
                else{
                    return response()->json([
                        'res' => true,
                        'msg' => 'Promotion is activated'
                    ], 201);
                }
            }
            else{
                return response()->json([
                    'msg' => 'The promotion does not exist'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => 'Error'
            ], 500);
        }
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
