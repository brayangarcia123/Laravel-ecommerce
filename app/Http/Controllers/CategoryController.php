<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Requests\CategoryStateUpdateRequest;
use App\Http\Requests\CategoryExistRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories=DB::select('CALL sp_list_category()');
        if($categories==[]){
            return response()->json([
                'res'=>false,
                'msg'=>'There are no categories'
            ]);
        }
        return response()->json([
                'res'=>true,
                'data'=>$categories
        ],200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryStoreRequest $request)

    {
        try{
            //Category::create($request->all()); //Solo para test
            $categories=DB::insert('CALL sp_add_categories(?,?)',[
                $request->name,
                $request->description
            ]);
            return response()->json([
                'res'=>true,
                'msg'=>'added successfully'
            ],201);

        }catch(\Exception $e){
            return response()->json([
                'res'=>false,
                'msg'=>$e->getMessage(),
            ],500);
        }
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
    public function update(CategoryUpdateRequest $request, $id)
    {

        try {
            $categoriesExist = DB::Select('Select * from categories where id = ?',[
                $id
            ]);

            if ($categoriesExist == []) {
                return response()->json([
                    'res' => false,
                    'msg' => 'The category does not exist'
                ],404);
            }

            $categories = DB::update('CALL sp_update_category(?,?,?)',[
                $id,
                $request->name,
                $request->description
            ]);
            return response()->json([
                'res'=>true,
                'msg'=>'Category updated'
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'res' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }


    }

        /**
     * Update State Category the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStateCategory(CategoryStateUpdateRequest $request, $id)
    {
        try {

            $categoriesExist = DB::Select('Select * from categories where id = ?',[
                $id
            ]);

            if ($categoriesExist == []) {
                return response()->json([
                    'res' => false,
                    'msg' => 'The category does not exist'
                ],404);
            }

            $categories=DB::update('CALL sp_update_is_actived_category(?,?)',[
                $id,
                $request->is_actived
            ]);
            return response()->json([
                'res'=>true,
                'msg'=>'Category state updated'
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'res' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }


    }

    public function existCategory($category_name)
    {
        try{

            $category_exist = DB::select('CALL sp_if_exists_category(?)',[
                $category_name,
            ]);      
            if(json_decode(json_encode($category_exist['0']), true)["`@exists_categories`"]==0){
                return response()->json([
                    'res' =>false,
                    'error' => 'The category does not exist'
                                            ], 404); 
            }
                $category_details = DB::select('select * from categories where name=(?)',[
                    $category_name
                ]);
                return response()->json([
                    "res" => true,
                    "msg" => 'The category exist',
                    "data" => $category_details,
                    ], 200);
                    
        }catch(\Exception $e){
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage(),
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
        //d
    }

    public function listSubcategoryByCategoryId($categories_id){
        try {
            $categories = DB::Select('Select * from categories where id = ?',[
                $categories_id
            ]);

            if ($categories == []) {
                return response()->json([
                    'res' => false,
                    'msg' => 'The category does not exist'
                ],404);
            }

            $subcategories=DB::Select('CALL sp_list_subcategory_by_category(?)',[
                $categories_id
            ]);

            if ($subcategories == []) {
                return response()->json([
                    'res' => false,
                    'msg' => 'There are not subcategories'
                ],204);
            }
            return response()->json([
                'res' => true,
                'msg' => $subcategories
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'res' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }
}
