<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function pruebas() {
        return "Acción de pruebas de USER-CONTROLLER";
    }

    public function register(Request $request) {
        /*
         *
         * Un API RESTful debe devolver un JSON, si no es así, es un servicio web normal
         *
         * Pasos:
         */
        //1. Recoger los datos del usario por post
        $json = $request->input('json', null);
        //var_dump($json);
        $params = json_decode($json);//Objeto
        //var_dump($params);
        //var_dump($params->name);
        $params_array = json_decode($json, true);//Array
        //var_dump($params_array);
        //var_dump($params_array['name']);

        //2. Validar datos
        //3. Cifrar la contraseña
        //4. Comprobar si el suario existe ya (Duplicado)
        //5. Crear el usuario
        $data = array(
            'status' => 'error',
            'code'   => 404,
            'message' => 'El usuario no se ha creado'
        );

        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {

    }
}
