<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

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
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});


/* can call these route after login , token need */ 

Route::middleware('auth:sanctum')->group(function () {
    
    /* Product and Category */
    Route::controller(ProductController::class)->prefix('product')->group(function(){
        /* Product Section */
        Route::get('/getAllProducts', 'getAllProducts');
        Route::post('/createProduct', 'createProduct');
        Route::delete('/deleteProduct/{id}', 'deleteProduct');
        Route::get('/getProductDataForUpdate/{id}', 'getProductDataForUpdate');
        Route::post('/updateProduct/{id}', 'updateProduct');

        /* Category Section */
        Route::post('/createCategory', 'createCategory');
        Route::get('/getAllCategories', 'getAllCategories');
        Route::delete('/deleteCategory/{id}', 'deleteCategory');
        Route::post('/updateCategory/{id}', 'updateCategory');
    });

    /* User Section such as customers, admins... */
    Route::controller(UserController::class)->prefix('user')->group(function(){
        Route::get('/getAllUsers', 'getAllUsers');
        Route::delete('/deleteUser/{id}', 'deleteUser');
        Route::post('/updateUser/{id}', 'updateUser');
        Route::post('/changeRole/{id}', 'changeRole');
        Route::post('/changePassword', 'changePassword');
    });

    /* Log out */
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});




