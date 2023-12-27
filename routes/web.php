<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\ResourceController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/demo/{name?}',function($name=Null){
//     // echo "THis is demo page in web api  Welcom ".$name;
//     $data=compact('name');
//     return view('demo')->with($data);
// });
Route::get('/',[DemoController::class,'index']);
Route::get('/about',[DemoController::class,'about']);
Route::resource('/photos',ResourceController::class);