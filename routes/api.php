<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DbInsertController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// ############################################################
// public routes
// ############################################################
// auth
Route::post('/login', [AuthController::class, "login"]);
Route::post('/register', [AuthController::class, "register"]);
// products
Route::get('/products/{paginate}', [PublicController::class, "getProducts"]);
Route::get('/products/single/{slug}', [PublicController::class, "getProductBySlug"]);
Route::get('/products/category/{category}/{paginate}', [PublicController::class, "getProductsByCategory"]);
Route::get('/products/search/{key}/{paginate}', [PublicController::class, "getProductsBySearch"]);

// categories
Route::get('/categories', [PublicController::class, "getCategories"]);
// ############################################################
// user routes
// ############################################################
Route::middleware(['auth:api'])->prefix("/user")->group(function () {
    Route::post('/logout', [AuthController::class, "logout"]);
    // carts
    Route::get('/carts', [UserController::class, "getCarts"]);
    Route::post('/carts', [UserController::class, "createCarts"]);
    Route::delete('/carts/{id}', [UserController::class, "deleteSingleCart"]);
    Route::delete('/carts', [UserController::class, "deleteAllCarts"]);
    // orders
    Route::get('/orders', [UserController::class, "getOrders"]);
    Route::post('/orders', [UserController::class, "createOrders"]);
});
// ############################################################
// db insert
// ############################################################
Route::post('/insert-products', [DbInsertController::class, "insertProducts"]);
Route::post('/insert-categories', [DbInsertController::class, "insertcategories"]);
