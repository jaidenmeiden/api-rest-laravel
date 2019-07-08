<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }


    public function index() {
        $categories = Category::all();

        return \response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        ]);
    }

    public function show($id) {
        $category = Category::find($id);

        if(is_object($category)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'category' => $category
            );
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'La categoría no existe'
            );
        }

        return \response()->json($data, $data['code']);
    }

    public function store(Request $request) {
        //1. Recoger los datos
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);//Array

        if(!empty($params_array)) {
            //2. Validar los datos
            $validate = \Validator::make($params_array, [
                'name' => 'required',
            ]);

            //2. Validar la contraseña
            if($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'No se ha guardado la categoría'
                );
            } else {
                //3. Guardar la categoría
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();

                //4. Devolver resultado
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No has enviado ninguna categoria'
            );
        }

        return \response()->json($data, $data['code']);
    }
}
