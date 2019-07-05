<?php

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

Route::get('/holamundo', function () {
    return '<h1>Hola mundo con laravel</h1>';
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/parametros/{nombre?}', function ($nombre = null) {
    $texto = '<h1>Texto en una ruta</h1>';
    if($nombre != null) {
        $texto .= 'Nombre ' . $nombre;
    } else {
        $texto .= 'No hay nombre';
    }
    return view('parametros', array(
        'texto' => $texto
    ));
});

Route::get('/animales', 'PruebasController@index');
Route::get('/posts', 'PruebasController@getPosts');
Route::get('/categories', 'PruebasController@getCategories');
