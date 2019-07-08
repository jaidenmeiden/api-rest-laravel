<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\User;
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
                'email' => 'required|email|unique:users',
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
                $pwd = hash('sha256', $params->password);

                //5. Comprobar si el usuario existe ya (Duplicado)
                //En validador al colocar la instrucción 'unique:users', se específica
                //que campo debe ser unico y en que tabla. Con esto se comprueba si un
                //usuario existe y si existe da un error de validación.


                //6. Crear el usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                //7. Guardar usuario
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
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
        //Se lama al servicio JwtAuth

        $jwtAuth = new \JwtAuth();

        //1. Recibir datos por POST
        $json = $request->input('json', null);
        $params = json_decode($json);//Objeto
        $params_array = json_decode($json, true);//Array
        var_dump($params_array);

        $validate = \Validator::make($params_array, [
            'email' => 'required|email',
            'password' => 'required ',
        ]);

        //2. Validar la contraseña
        if($validate->fails()) {
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        } else {
            //3. Cifrar la contraseña
            $pwd = hash('sha256', $params->password);

            //4. Devolver token o datos
            $signup = $jwtAuth->singup($params->email, $pwd);
            if(!empty($params->gettoken)) {
                $signup = $jwtAuth->singup($params->email, $pwd, true);
            }
        }

        return response()->json($signup, 200);
    }
}
