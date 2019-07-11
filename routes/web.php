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

//Cargando clases
use \App\Http\Middleware\ApiAuthMiddleware;

//Rutas de pruebas Framework
Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/holamundo', function () {
    return '<h1>Hola mundo con laravel</h1>';
});

Route::get('/pruebas/parametros/{nombre?}', function ($nombre = null) {
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

Route::get('/pruebas/animales', 'PruebasController@index');
Route::get('/pruebas/posts', 'PruebasController@getPosts');
Route::get('/pruebas/categories', 'PruebasController@getCategories');

//Rutas del API

/*
 * Metodos HTTP comunes
 *
 * GET: Conseguir datos o recursos
 * POST: Guardar datos o recursos o hacer logica desde un formulario
 * PUT: Actualizar datos o recursos
 * DELETE: Eliminar datos o recursos
 *
 * API REST: Solo utiliza GET y POST
 *
 * API RESTful: Utiliza todos los metodos HTTP
 *
 */

//Rutas de pruebas Controladores
//Route::get('/user/prueba', 'UserController@pruebas');
//Route::get('/category/prueba', 'CategoryController@pruebas');
//Route::get('/post/prueba', 'PostController@pruebas');

//Rutas del controlador de usuario
/*
 * No se puede acceder a las rutas POST desde el navegador, ya
 * que este tipo de acceso es GET y la petición es POST.
 *
 * Para poder acceder a dicha ruta se debe llamr desde un formulario HTML
 * o un cliente REST o cliente RESTful
 */
Route::post('/api/user/register', 'UserController@register');
Route::post('/api/user/login', 'UserController@login');
Route::put('/api/user/update', 'UserController@update');
Route::post('/api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
Route::get('/api/user/detail/{id}', 'UserController@detail');

//Rutas del controlador de categorías
Route::resource('/api/category', 'CategoryController');

//Rutas del controlador de post
Route::resource('/api/post', 'PostController');
Route::post('/api/post/upload', 'PostController@upload');
Route::get('/api/post/image/{filename}', 'PostController@getImage');