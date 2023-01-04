<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateStockRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $lista = DB::select('select * from stocks where id=(?)',[
            $id
        ]);
        $collectlista = collect($lista);

        if($lista==[]){
            return response()->json([
                'res' =>false,
                'error' => 'Stock does not exist.'
                                        ], 500);
        }else{
            return response()->json([
                "res" => true,
                "data" => $lista,
                ], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(UpdateStockRequest $request,$id)
    {
        // $stock = Stock::findOrFail($request->id);
        try{
            // $stock = Stock::findOrFail($request->id);
            $stocks = DB::update('update stocks set quantity = ?,
            update_at = ?, products_id = ? where id = ?',[
                $request->quantity,
                $request->update_at,
                $request->products_id,
                $id
            ]);
            if($stocks){
                return response()->json([
                    'msg' => 'stock modified'
                ], 200);
            }else{
                return response()->json([
                    'msg' => 'The stock does not exist'
                ], 404);
            }
            
               
        }catch(\Exception $e){
            return response()->json([
                'msg' => 'Error modifying stock'
            ], 400);
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
