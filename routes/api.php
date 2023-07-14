<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\Customer\ProfileController;

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

/* Register and LogIn , no need token */

Route::controller(AuthController::class)->prefix('auth')->group(function(){
    Route::post('/admin/register',                      'adminRegister');
    Route::post('/register',                            'customerRegister');
    Route::post('/login',                               'login');
    Route::get('/logout',                               'logout')->middleware('auth:sanctum');

});

/* can call these route after login , token need */

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

     /* Product and Category */
     Route::controller(CategoryController::class)->prefix('category')->group(function(){
        /* Category Section */
        Route::post('/createCategory',                  'createCategory');
        Route::get('/getAllCategories',                 'getAllCategories');
        Route::delete('/deleteCategory/{id}',           'deleteCategory');
        Route::post('/updateCategory/{id}',             'updateCategory');
        Route::get('/takeDataToEdit/{id}',              'takeDataToEdit');
        Route::get('/takeCategories',                   'takeCategories');
        Route::get('/getAllCategories/{searchKey}',     'getAllCategories');
    });

    /* Product and Category */
    Route::controller(ProductController::class)->prefix('product')->group(function(){
        /* Product Section */
        Route::get('/getAllProducts',                   'getAllProducts');
        Route::post('/createProduct',                   'createProduct');
        Route::delete('/deleteProduct/{id}',            'deleteProduct');
        Route::get('/getProductDataForUpdate/{id}',     'getProductDataForUpdate');
        Route::post('/updateProduct',                   'updateProduct');
        Route::get('/getAllProducts/{searchKey}',       'getAllProducts');
        Route::get('/filterProducts/{id}',              'filterProductsByCategory');
    });

    /* User Section such as customers, admins... */
    Route::controller(UserController::class)->prefix('user')->group(function(){
        Route::get('/getAllAdmins',                     'getAllAdmins');
        Route::get('/getAllCustomers',                  'getAllCustomers');
        Route::get('/getMyProfile/{id}',                'getMyProfile');
        Route::delete('/deleteUser/{id}',               'deleteUser');
        Route::post('/updateUser/{id}',                 'updateUser');
        Route::post('/changeRole',                      'changeRole');
        Route::post('/changePassword',                  'changePassword');
        Route::get('/getAllCustomers/{searchKey}',      'getAllCustomers');
    });

});

Route::middleware('auth:sanctum')->group(function () {

    Route::controller(ProfileController::class)->prefix('user')->group(function(){
        Route::get('getProfileData',                    'getProfileData');
    });
});




