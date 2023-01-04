<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CouponAddRequest;
use App\Http\Requests\CouponExistsRequest;
use App\Http\Requests\CouponIsValidRequest;
use App\Http\Requests\CouponBySellerExistRequest;
use App\Http\Requests\CouponStateUpdateRequest;
use App\Http\Requests\CouponUpdateRequest;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{

    public function index()
    {
        $Coupon=DB::select("select
                                *
                            from coupons order by type_discount;");
        $collection=collect($Coupon);
        return response()->json(
            [
                'res'=>true,
                'data'=>$collection
            ],200);
    }

    public function listCouponsBySeller(CouponBySellerExistRequest $request)
    {
        $Coupon=DB::select("call sp_list_coupons_by_seller(?);",[
            $request->users_id,
        ]);
        $collection=collect($Coupon);
        return response()->json(
            [
                'res'=>true,
                'data'=>$collection
            ],200);
    }

    public function addCoupon(CouponAddRequest $request)
    {
        try{
            $coupons_code = DB::select('select code from coupons where code=(?)',[
                $request->code,
            ]);
             if($coupons_code==[]){
                 $resultado = DB::statement('CALL sp_add_coupons(?,?,?,?,?,?,?)',[
                    $request->code,
                    $request->start_date,
                    $request->ending_date,
                    $request->type_discount,
                    $request->description,
                    $request->users_id,
                    $request->cupons_types_id,
                 ]);
                return response()->json([
                    'res' => true,
                    'msg' => "Coupon created",
                ], 201);
             }
                return response()->json([
                    'res' =>false,
                    'error' => 'Duplicate coupon code'
                                            ], 400);

        }catch(\Exception $e){
            return response()->json([
                'res' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function existCoupon($coupon_id)
    {
        try{
            $coupon_exist = DB::select('CALL sp_if_exists_coupon(?)',[
                $coupon_id,
            ]);

            if(json_decode(json_encode($coupon_exist['0']), true)["`@exists_coupon`"]==0){
                return response()->json([
                    'res' =>false,
                    'error' => 'The coupon does not exist'
                                            ], 404);
            }
                $coupon = DB::select('select * from coupons where id=(?)',[
                    $coupon_id
                ]);
                return response()->json([
                    "res" => true,
                    "msg" => 'The coupon exist',
                    "data" => $coupon,
                    ], 200);

        }catch(\Throwable $e){
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    public function isValidCoupon($coupon_code)
    {
        try{
            $coupon_exist = DB::select('select * from coupons where code = (?)',[
                $coupon_code,
            ]);

            if($coupon_exist==[]){
                return response()->json([
                    'res' =>false,
                    'error' => 'The coupon does not exist'
                                            ], 404);
            }else{
                $coupon_isValid = DB::select('CALL sp_if_coupon_is_used(?)',[
                    $coupon_code,
                ]);

                if(json_decode(json_encode($coupon_isValid['0']), true)["`@coupon_used`"]==1){
                    return response()->json([
                        'res' =>false,
                        'error' => 'The coupon was used',
                                                ], 400);
                }else{
                    return response()->json([
                        'res' =>true,
                        'msg' => 'The coupon is valid',
                                                ], 200);
                }
            }
        }catch(\Exception $e){
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }

    }

    public function update(CouponUpdateRequest $request, $coupon_id)
    {
        try {

            $coupons = DB::select('select * from coupons where id = ?', [
                $coupon_id
            ]);

            if($coupons == []){
                return response()->json([
                    'res' =>false,
                    'error' => 'The coupon does not exist'
                    ], 404);
            }else{
                $coupons_up = DB::select('select * from coupons where id = ? && is_actived = "1"',[
                    $coupon_id
                ]);

                if($coupons_up == [])
                {
                    return response()->json([
                        'res' =>false,
                        'error' => 'The coupon does not exist'
                        ], 404);
                }
                else
                {
                    DB::update('call sp_update_coupon (?, ?, ?, ?, ?)',[
                        $coupon_id,
                        $request -> start_date,
                        $request -> ending_date,
                        $request -> type_discount,
                        $request -> description,
                    ]);

                        return response()->json([
                            'res' => true,
                            'msg' => 'Coupon modified',
                        ], 201);
                }
            }

        }
        catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'msg'=> 'Error modifying coupon'
            ], 500);
        }
    }

        /**
     * Update State Coupon the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function updateStateCoupon (CouponStateUpdateRequest $request, $coupon_id)
    {
        try {
            $coupons = DB::update('call sp_update_is_actived_coupon (?, ?)',[
                $coupon_id,
                $request -> is_actived
            ]);

            Log::info($coupons);
            if ($coupons)
            {
                $coupons_st = DB::select('select * from coupons where id = ? && is_actived = "1"',[
                    $coupon_id
                ]);

                if($coupons_st == [])
                {
                    return response()->json([
                        'res' => true,
                        'msg' => 'Coupon is deactivated'
                    ], 201);
                }
                else{
                    return response()->json([
                        'res' => true,
                        'msg' => 'Coupon is activated'
                    ], 201);
                }
            }
            else{
                return response()->json([
                    'msg' => 'The coupon does not exist'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => 'Error'
            ], 500);
        }
    }

    public function delete($id)
    {
        $cuponborrado = DB::Select('delete from coupons where id=(?) ', [$id]);

        return response()->json([
            'msg' => 'Se ha eliminado el cupon exitosamente',
        ], 200);
    }
}
