<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\Http\Controllers\api\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/demo',function(){
    $result["response"]="This is output";
    return response()->json($result);
});

Route::post('/demo',function(){
    $result['response']="This is a Post Request";
    return response()->json($result);

});
Route::delete('/demo/{id}',function($id){
    return response($id,200);
});

Route::get('/test',function(){
    p("Working");
});

Route::post('user/store','App\Http\Controllers\api\UserController@store');

Route::get('user/getData/{flag}',[UserController::class,'index']);

//Route::get('user/getUserById/{id}',[UserController::class,'show']);//comment out for testing middleware

Route::delete('user/deleteUserById/{id}',[UserController::class,'destroy']);

Route::put('user/updateUserById/{id}',[UserController::class,'update']);

Route::post('/register',[UserController::class,'register']);

Route::post('/login',[UserController::class,'login']);

Route::middleware('auth:api')->group(function(){
    Route::get('user/getUserById/{id}',[UserController::class,'show']);
});