<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [\App\Http\Controllers\Api\RegisterController::class, 'register']);
Route::get('noauth', [\App\Http\Controllers\Api\RegisterController::class, 'noauth'])->name('noauth');

Route::any('login', [\App\Http\Controllers\Api\RegisterController::class, 'login'])->name('login');
Route::any('verify', [\App\Http\Controllers\Api\RegisterController::class, 'verify']);
Route::post('password/email',  [\App\Http\Controllers\Api\ForgotPasswordController::class,'forget']);
Route::any('password/reset', [\App\Http\Controllers\Api\CodeCheckController::class,'index']);
Route::post('password/code/check', [\App\Http\Controllers\Api\CodeCheckController::class,'code_verify']);


Route::get('products/{vendorID}', [\App\Http\Controllers\Api\RegisterController::class, 'products']);
Route::get('vendor/list', [\App\Http\Controllers\Api\OrderController::class, 'vendor_list']);


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
Route::group(['middleware' => ['api','auth:sanctum'], 'prefix' => 'auth'], function () {

    Route::get('wallet', [\App\Http\Controllers\Api\OrderController::class, 'wallet']);
    Route::get('vendor/order/list', [\App\Http\Controllers\Api\OrderController::class, 'vendor_order']);
    Route::post('vendor/oderr_status/{orderId}', [App\Http\Controllers\Api\OrderController::class, 'order_status']);
    Route::get('order/list', [\App\Http\Controllers\Api\OrderController::class, 'user_order']);
    Route::post('/checkout', [App\Http\Controllers\Api\OrderController::class, 'store']);

    Route::get('user', [\App\Http\Controllers\Api\RegisterController::class, 'user']);
    Route::post('user/update', [\App\Http\Controllers\Api\RegisterController::class, 'user_update']);
    
    Route::resource('product', \App\Http\Controllers\Api\ProductController::class);
    Route::resource('service', \App\Http\Controllers\Api\ServiceController::class);
    Route::post('profile', [\App\Http\Controllers\Api\RegisterController::class, 'profile']);  

});
