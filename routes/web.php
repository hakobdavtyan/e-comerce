<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});


    Route::match(['get','post'],'/',[\App\http\Controllers\IndexController::class,'index']);
    Route::get('/products/{id}',[\App\Http\Controllers\ProductsController::class,'products']);
    Route::get('/categories/{category_id}',[\App\Http\Controllers\IndexController::class,'categories']);
    Route::get('/get-product-price',[\App\Http\Controllers\ProductsController::class,'getprice']);
    //Route for Login-register
    Route::get('/login-register',[\App\Http\Controllers\UsersController::class,'userLoginRegister']);
    //Route for add users registration
    Route::post('/user-register',[\App\Http\Controllers\UsersController::class,'register']);
//Route for add users registration
    Route::get('/user-logout',[\App\Http\Controllers\UsersController::class,'logout']);
    // Route for add to cart
    Route::match(['get','post'],'/cart',[\App\http\Controllers\ProductsController::class,'cart']);
    // Route for cart
    Route::match(['get','post'],'add-cart',[\App\http\Controllers\ProductsController::class,'addtoCart']);
    //Route For Delete Cart Product
    Route::get('/cart/delete-product/{id}',[\App\Http\Controllers\ProductsController::class,'deleteCartProduct']);
    //Route For update Quantity
    Route::get('/cart/update-quantity/{id}/{quantity}',[\App\Http\Controllers\ProductsController::class,'updateCartQuantity']);
    //Apply Coupon Code
    Route::post('cart/apply-coupon',[\App\Http\Controllers\CategoryController::class,'applyCoupon']);
    Route::match(['get','post'],'/admin',[\App\Http\Controllers\AdminController::class,'login']);

    Auth::routes();

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::group(['middleware'=>['auth']],function (){

    //Category Route
    Route::match(['get','post'],'/admin/add_category',[\App\Http\Controllers\CategoryController::class,'addCategory']);
    Route::match(['get','post'],'/admin/view_categories',[\App\Http\Controllers\CategoryController::class,'viewCategories']);
    Route::match(['get','post'],'/admin/edit_category/{id}',[\App\Http\Controllers\CategoryController::class,'editCategory']);
    Route::match(['get','post'],'/admin/delete_category/{id}',[\App\Http\Controllers\CategoryController::class,'deleteCategory']);
    Route::post('/admin/update-category-status',[\App\Http\Controllers\CategoryController::class,'updateStatus']);

    //Product Route
    Route::match(['get','post'],'/admin/dashboard/',[\App\Http\Controllers\AdminController::class,'dashboard']);
    Route::match(['get','post'],'/admin/add-product/',[\App\Http\Controllers\ProductsController::class,'addProduct']);
    Route::match(['get','post'],'/admin/view-products/',[\App\Http\Controllers\ProductsController::class,'viewProducts']);
    Route::match(['get','post'],'/admin/edit-product/{id}',[\App\Http\Controllers\ProductsController::class,'editProducts']);
    Route::match(['get','post'],'/admin/delete-product/{id}',[\App\Http\Controllers\ProductsController::class,'deleteProduct']);
    Route::match(['get','post'],'/admin/update-product/{id}',[\App\Http\Controllers\ProductsController::class,'updateProduct']);
    Route::post('/admin/update-product-status',[\App\Http\Controllers\ProductsController::class,'updateStatus']);
    Route::post('/admin/update-featured-product-status',[\App\Http\Controllers\ProductsController::class,'updateFeatured']);

//Products Attributes
    Route::match(['get','post'],'/admin/add_attributes/{id}/',[\App\Http\Controllers\ProductsController::class,'addAttributes']);
    Route::get('/admin/delete_attributes/{id}/',[\App\Http\Controllers\ProductsController::class,'deleteAttributes']);
    Route::match(['get','post'],'/admin/edit_attributes/{id}/',[\App\Http\Controllers\ProductsController::class,'editAttributes']);
    Route::match(['get','post'],'/admin/add_images/{id}/',[\App\Http\Controllers\ProductsController::class,'addImages']);
    Route::get('/admin/delete_alt_image/{id}/',[\App\Http\Controllers\ProductsController::class,'deleteAltImage']);

//Banners Route
    Route::match(['get','post'],'/admin/banners',[\App\Http\Controllers\BannersController::class,'banners']);
    Route::match(['get','post'],'/admin/add_banner',[\App\Http\Controllers\BannersController::class,'addBanners']);
    Route::match(['get','post'],'/admin/edit-banners/{id}',[\App\Http\Controllers\BannersController::class,'editBanners']);
    Route::match(['get','post'],'/admin/edit-banner/{id}',[\App\Http\Controllers\BannersController::class,'editBanner']);

    //Coupons Route
    Route::match(['get','post'],'/admin/add-coupon',[\App\Http\Controllers\CouponsController::class,'addCoupon']);
    Route::match(['get','post'],'/admin/view-coupons',[\App\Http\Controllers\CouponsController::class,'viewCoupons']);
    Route::match(['get','post'],'/admin/edit-coupons/{id}',[\App\Http\Controllers\CouponsController::class,'editCoupons']);
    Route::get('/admin/delete-coupons/{id}/',[\App\Http\Controllers\CouponsController::class,'deleteCoupon']);
    Route::post('/admin/update-coupon-status',[\App\Http\Controllers\ProductsController::class,'updateStatus']);
});
    Route::get('logout',[\App\Http\Controllers\AdminController::class,'logout']);
