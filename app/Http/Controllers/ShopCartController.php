<?php

namespace App\Http\Controllers;
use App\Http\Requests\AddProductsShopcart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Products;

class ShopCartController extends Controller
{

    //---------------------------------------------------------------------------------
    //find products by the name
    public function find($name)
    {
        $resultado=DB::select('CALL sp_search_product(?)',[$name]);
        //$collection=collect($categories);
        if($resultado==[]){
            return response()->json([
                "msg" => "the product doesn't exist "
                ], 404);  
        }else{
            return response()->json([
                "data" => $resultado,
                "msg" => "it exist"
                ], 200);
        }
    }
    //ADD PRUCTS TO THE SHOPCART
    public function addProductsToShopcart(AddProductsShopcart $request)
    {
        DB::insert('CALL sp_add_shopcarts(?,?,?)',[
            $request->quantity,
            $request->products_id,
            $request->users_id,
         ]);
         return response()->json([
             'respuesta' => true,
             'msg' => "Correct insertion in the cartshop"
         ], 201);
    }
    //OREDER PRODUCTS
     public function Order()
    {
        $lista = DB::select('CALL sp_order_products()');
        $collectlista = collect($lista);

        if($lista==[]){
            return response()->json([
                'res' =>false,
                'error' => 'Does not existe'
                                        ], 500);
        }else{
            return response()->json([
                "res" => true,
                "data" => $lista,
                ], 200);
        }
    }
}
