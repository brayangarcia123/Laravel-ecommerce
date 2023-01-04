<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubCategoriebaRequest;
use App\Http\Requests\SubCategoryTestUpdateRequest;
use App\Http\Requests\SubcategoryUpdateState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNan;

class SubCategoriaController extends Controller
{

    public function insert(SubCategoriebaRequest $request)
    {
        try{
            $subcategories=DB::select('CALL sp_add_subcategories(?,?,?)',[
                $request->name,
                $request->description,
                $request->categories_id
            ]);
            $id = $subcategories[0] -> id;
            return response()->json([
                'res'=>true,
                'msg'=>'added successfully',
                'data' => array("id"=>$id)
            ],201);

        }catch(\Exception $e){
            return response()->json([
                'res'=>false,
                'msg'=>$e->getMessage(),
            ],500);
        }
    }

    public function update(SubCategoryTestUpdateRequest $request, $id)
    {
        try {
            $subcategoriesExist = DB::Select('Select * from subcategories where id = ?',[
                $id
            ]);

            if ($subcategoriesExist == []) {
                return response()->json([
                    'res' => false,
                    'msg' => 'The subcategory does not exist'
                ],404);
            }
            
            $subcategories = DB::update('UPDATE subcategories SET name = ?, categories_id = ? WHERE id = ? and is_actived = 1',[
                $request->name,
                $request->categories_id,
                $id
            ]);
            return response()->json([
                'res'=>true,
                'msg'=>'subcategory updated'
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'res' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }


    public function delete($id)
    {
        $subCategorie = DB::table('subcategories')->where('id', $id)->delete();
        return response()->json(['response' => 'ok ,se elimino', 'msg' => '200', 'data' => $id], 200);
    }

    public function viewList()
    {
        $subcategories=DB::select('CALL sp_list_subcategory()');
        if($subcategories==[]){
            return response()->json([
                'res'=>false,
                'msg'=>'There are no subcategories'
            ]);
        }
        return response()->json([
                'res'=>true,
                'data'=>$subcategories
        ],200);
        /*
        $subCategorie = DB::table('subcategories')->get();
        */
    }

    public function viewId($id)
    {
        $subCategorie = DB::table('subcategories')->where('id', $id)->get();
        return response()->json(['response' => 'ok datos capturado', 'msg' => '200', 'data' => $subCategorie], 200);
    }

    public function stateModify(SubcategoryUpdateState $request, $id){

        try {

            $subcategoriesExist = DB::Select('Select * from subcategories where id = ?',[
                $id
            ]);

            if ($subcategoriesExist == []) {
                return response()->json([
                    'res' => false,
                    'msg' => 'The subcategory does not exist'
                ],404);
            }

            $categories=DB::update('CALL sp_update_is_actived_subcategory(?,?)',[
                $id,
                $request->is_actived
            ]);
            return response()->json([
                'res'=>true,
                'msg'=>'subcategory state updated'
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'res' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function existSubCategory($subcategory_name)
    {
        try{

            $subcategory_exist = DB::select('CALL sp_if_exists_subcategory(?)',[
                $subcategory_name,
            ]);      
            if(json_decode(json_encode($subcategory_exist['0']), true)["`@exists_subcategories`"]==0){
                return response()->json([
                    'res' =>false,
                    'error' => 'The subcategory does not exist'
                                            ], 404); 
            }
                $subcategory_details = DB::select('select * from subcategories where name=(?)',[
                    $subcategory_name
                ]);
                return response()->json([
                    "res" => true,
                    "msg" => 'The subcategory exist',
                    "data" => $subcategory_details,
                    ], 200);
                    
        }catch(\Exception $e){
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

}
