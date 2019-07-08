<?php

namespace App\Http\Controllers;

use http\Env\Response;
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

        if(!empty($params) && !empty($params_array)) {
            //2. Limpiar datos
            $params_array = array_map('trim', $params_array);

            //3. Validar datos
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email',
                'password' => 'required ',
            ]);

            if($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            } else {
                //4. Cifrar la contraseña
                //5. Comprobar si el suario existe ya (Duplicado)
                //6. Crear el usuario

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente'
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {

    }
}
