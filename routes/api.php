<?php

use App\Http\Controllers\SaveOrderController;
use App\Http\Middleware\GetUserMiddleware;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/orders',[SaveOrderController::class,"getListOrder"])->middleware([
    GetUserMiddleware::class
]);

Route::post('/saveOrder',[SaveOrderController::class,'saveOrder']);
Route::get('/getProductBuyMonth',[SaveOrderController::class,'getProductBuyMonth']);
