<?php

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

Route::get('/categories', 'Menu\CategoriesController@index');
Route::get('/categories/{category}/positions', 'Menu\CategoriesController@positions');
Route::post('/orders', 'Menu\OrderController@create');
Route::patch('/orders/{token}', 'Menu\OrderController@patch');
Route::get('/orders/{token}', 'Menu\OrderController@view');
Route::get('/position-options', 'Menu\OptionsController@index');
