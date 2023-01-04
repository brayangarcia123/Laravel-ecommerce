<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChangePermissionsController;
use App\Http\Controllers\StockController;

use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubCategoriaController;
use App\Http\Controllers\PermissionController;

//use App\Http\Controllers\API\ProductsController;
use App\Http\Controllers\ShopCartController;
use App\Http\Controllers\TokenAutenticarController;

use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('singup/',[TokenAutenticarController::class,'singup']);
Route::post('login/',[TokenAutenticarController::class,'login']);

Route::middleware('isBuyer')->group(function(){
    Route::post('buyerToSeller',[ChangePermissionsController::class,'buyerToSeller']);
    // Route::get('categories',[CategoryController::class,'index']);
    // Route::get('products/search', [ProductController::class, 'findNProductxCategory']);

});

Route::middleware(['isSeller'])->group(function(){
    Route::post('sellerToBuyer',[ChangePermissionsController::class,'sellerToBuyer']);
   // Route::get('categories',[CategoryController::class,'index']);
   // Route::post('products', [ProductController::class, 'store']);
   // Route::get('products/search', [ProductController::class, 'findNProductxCategory']);

});
Route::middleware(['isAdmin'])->group(function(){
   // Route::get('categories',[CategoryController::class,'index']);
   // Route::get('products/search', [ProductController::class, 'findNProductxCategory']);

});
Route::middleware('isAdminOrSeller')->group(function(){
    // Route::get('categories',[CategoryController::class,'index']);
 });
Route::middleware(['isBuyerOrSeller'])->group(function(){
    Route::post('refresh/',[TokenAutenticarController::class,'refreshToken']);
    Route::post('logout/',[TokenAutenticarController::class,'logout']);
    //Route::get('categories',[CategoryController::class,'index']);
});



Route::get('categories',[CategoryController::class,'index']);
Route::post('categories',[CategoryController::class,'store']);
Route::put('categories/{id}',[CategoryController::class,'update']);
Route::put('categories/state/{id}',[CategoryController::class,'updateStateCategory']);
Route::get('category/{category_name}/exist',[CategoryController::class,'existCategory']);

Route::put('categories/{category_id}',[CategoryController::class,'update']);
//Route::put('categories/state/{category_id}',[CategoryController::class,'updateStateCategory']);
Route::get('categories/{categories_id}/subcategories',[CategoryController::class,'listSubcategoryByCategoryId']);

//Product
Route::get('products', [ProductController::class,'index']);
Route::get('products/search', [ProductController::class, 'findNProductxCategory']);
Route::post('products', [ProductController::class, 'store']);
Route::get('products/{id}', [ProductController::class,'findProductid']);
Route::put('products/{id}', [ProductController::class,'update']);
Route::delete('products/{id}', [ProductController::class,'delete']);
Route::get('productsname/{name}',[ProductController::class,'findProductname']);
Route::get('productscategory/{category}',[ProductController::class,'findProductCategory']);
Route::get('productssubcategory/{subCategoryId}', [ProductController::class, 'findProductBySubCategory']);
Route::get('productsvalidate',[ProductController::class,'validateProduct']);
Route::get('filtrateproducts',[ProductController::class,'getFiltratedProducts']);

Route::get('promotion',[PromotionController::class,'index']);
Route::get('promotion/{users_id}',[PromotionController::class,'listPromotionBySeller']);
Route::post('promotions/add', [PromotionController::class, 'addPromotion']);
Route::put('promotions/{promotion_id}',[PromotionController::class, 'update']);
Route::put('promotions/state/{promotion_id}',[PromotionController::class,'updateStatePromotion']);
Route::get('promotion/{promotion_id}/exist', [PromotionController::class, 'existPromotion']);

Route::get('seller/{seller_id}/details', [SellerController::class, 'detailsSeller']);
Route::post('seller/{seller_id}/valuation', [SellerController::class, 'sendValuation']);
Route::get('valuation/{valuation_id}', [SellerController::class, 'oneValuation']);
Route::get('valuation', [SellerController::class, 'allValuation']);


Route::get('coupon',[CouponController::class,'index']);
Route::get('coupon/{coupon_id}/exist', [CouponController::class, 'existCoupon']);
Route::get('coupon/{coupon_code}/state', [CouponController::class, 'isValidCoupon']);
Route::post('coupon', [CouponController::class, 'addCoupon']);
Route::put('coupon/{coupon_id}',[CouponController::class, 'update']);
Route::put('coupon/state/{coupon_id}',[CouponController::class,'updateStateCoupon']);
Route::delete('/coupon/{id}', [CouponController::class ,'delete']);

Route::post('/subCategories', [SubCategoriaController::class ,'insert']);
Route::put('/subCategories/{id}', [SubCategoriaController::class ,'update']);
Route::delete('/subCategories/{id}', [SubCategoriaController::class ,'delete']);
Route::get('/subCategories', [SubCategoriaController::class ,'viewList']);
Route::get('/subCategories/{id}', [SubCategoriaController::class ,'viewId']);
Route::get('/subCategories/{category_name}/exist',[SubCategoriaController::class,'existSubCategory']);

Route::put('/subCategories/state/{id}',[SubCategoriaController::class,'stateModify']);

//Stocks
Route::get('stocks/{id}', [StockController::class, 'index']);
Route::put('stocks/{id}', [StockController::class, 'update']);

//ShopCart


Route::get('products/search/{name}', [ShopCartController::class,'find']);
Route::post('shopcart',[ShopCartController::class,'addProductsToShopcart']);
Route::get('OrderProducts',[ShopCartController::class,'Order']);


Route::get('User/exist/{mail}', [UserController::class, 'show']);

Route::get('permissions/user/{user_id}',[PermissionController::class,'checkAdminPermissions']);
