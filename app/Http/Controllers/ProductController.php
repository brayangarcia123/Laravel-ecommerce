<?php
namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\Requests\ProductAddRequest;
use App\Http\Requests\ProductFindNxCategoryRequest;
use Facade\Ignition\QueryRecorder\Query;

use App\Http\Requests\AddProductsShopcart;

class ProductController extends Controller
{
    /**
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function findNProductxCategory(ProductFindNxCategoryRequest $request)
    {
        $quantity = $request->query('quantity');
        $category = $request->query('category');

        if ($quantity == null){
            $quantity = 30;
        }

        $lista = DB::select('
        select * from products p inner join subcategories s
        on p.Subcategories_id = s.id
        inner join categories c
        on s.Categories_id = c.id
        where c.name = ? limit ?;',
        [
            $category,
            $quantity
        ]);

        if ($lista == []){
            return response()->json([
                "code"    => 404,
                "status"  => "Not Found",
                "message" => "The server can not find the requested resource.",
                "error"   => ["Category : ".$category." not found"]
            ],404);
        }

        return response()->json([
            "code"    => 200,
            "status"  => "Ok",
            "message" => "The request to search for ".$quantity." Product by ".$category." successful.",
            "data"    => $lista
        ], 200);
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     */

    public function store(ProductAddRequest $request)
    {
        try{

            DB::insert('call sp_add_products(?,?,?,?,?,?,?,?,?)',[
                $request->name,
                $request->price,
                $request->stock,
                $request->img_url,
                $request->description,
                $request->subcategories_id,
                $request->promotions_id,
                $request->brands_id,
                $request->users_id]);

            return response()->json([
                "code"    => 201,
                "status"  => "Created",
                "message" => "The request succeeded, and create new product"
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $Product=DB::select("select * from products");
        $collection=collect($Product);
        return response()->json(
            [
                'res'=>true,
                'data'=>$collection
            ],200);
    }

    //Metodo para visualizar un producto por id
    public function findProductid($id)
    {
        $lista = DB::Select('Call sp_list_product_specific(?)', [$id]);

        if ($lista == []) {
            return response()->json([
                "msg" => "No se ha encontrado el producto"
            ], 404);
        }

        return response()->json([
            'msg' => 'Se ha listado el producto filtrado',
            'respuesta' => $lista
        ], 200);
    }

    public function validateProduct(Request $request)
        {
            try{
            // sp_search_product
            $products=DB::select('call sp_if_exists_product2(?)',[
                $request->name,
                // $request->price,
                // $request->cost,
                // $request->img_url,
                // $request->description,
                // $request->Subcategories_id,
                // $request->Promotions_id,
            ]);
            $collection=collect($products);
            if($products==[]){
                return response()->json([
                    'res' =>false,
                    'error' => 'Product does not exist.'
                                            ], 500);
            }else{
                return response()->json([
                    'res'=>true,
                    'data'=>$collection,
                    'msg'=>'Product exists.'
                ],200);
            }
            }catch(\Exception $e){
                return response()->json([
                    'res' => false,
                    'msg' => 'Error searching product'
                   // 'msg' => $e->getMessage(),
                ], 400);
            }
        }


    //Metodo para visualizar un producto por nombre
    public function findProductname($name){

        $lista = DB::Select("Call sp_if_exists_product(?)", [$name]);

        if ($lista == []) {
            return response()->json([
                "msg" => "No se ha encontrado el producto"
            ], 404);
        }

        return response()->json([
            'msg' => 'Se ha listado el producto filtrado '. $name,
            'respuesta' => $lista
        ], 200);
    }

    //Metodo para visualizar un producto por categoria
    public function findProductCategory($category){

        $lista = DB::Select("select c.* from products p
        inner join subcategories s on p.SubCategories_id=s.id
        inner join categories c on s.Categories_id=c.id
        where p.name=?", [$category]);

        if ($lista == []) {
            return response()->json([
                "msg" => "No se ha encontrado la categoria"
            ], 404);
        }

        return response()->json([
            'msg' => 'Se ha listado el producto filtrado',
            'respuesta' => $lista
        ], 200);

    }

    public function findProductBySubCategory($subCategoryId)
    {

        $lista = DB::Select("CALL sp_list_products_by_subcategory(?)", [$subCategoryId]);

        if (isset($lista[0]->{"MENSAJE"})) {
            return response()->json([
                "msg" => $lista[0]->{"MENSAJE"}
            ], 404);
        }
        return response()->json([
            'msg' => 'Lista de productos de la subcategoria',
            'respuesta' => $lista
        ], 200);
    }
    //listar productos filtrados
    public function getFiltratedProducts(Request $request){
        $initial_range=$request->query('initital_range');
        $final_range=$request->query('final_range');
        $category=$request->query('category');
        $subCategory=$request->query('subCategory');
        $brand=$request->query('brand');

        $query = $request->all();
        if ($initial_range==''||$final_range==''||$category==''||$subCategory==''||$brand=='') {
            return response()->json([
                "msg" => "wrong query parameters"
            ], 400);
        }
        $lista = DB::Select("CALL sp_list_filtrated_products(?,?,?,?,?)", [$initial_range,$final_range,$category,$subCategory,$brand]);
        if (isset($lista[0]->{"MENSAJE"})) {
            return response()->json([
                "msg" => $lista[0]->{"MENSAJE"}
            ], 404);
        }
        return response()->json([
            'msg' => 'Lista de productos filtrados',
            'respuesta' => $lista
        ], 200);
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

        $lista = DB::select("Call sp_update_product(?,?,?,?,?)", [
            $id,
            $request->name,
            $request->price,
            $request->img_url,
            $request->description,]);


        return response()->json([
            'msg' => 'Se ha actualizado el producto exitosamente',
            'respuesta' => $lista
        ], 200);

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
    public function delete($id)
    {
        $lista = DB::Select('Call sp_delete_product(?)', [$id]);

        return response()->json([
            'msg' => 'Se ha eliminado el producto exitosamente',
        ], 200);

    }
}