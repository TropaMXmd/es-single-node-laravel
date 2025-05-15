<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group([
    'namespace' => 'App\Http\Controllers\Api'
], function () {
    Route::get('/products', 'ProductController@index');
    Route::get('/products/{id}', 'ProductController@show');
    Route::post('/products', 'ProductController@store');
});
