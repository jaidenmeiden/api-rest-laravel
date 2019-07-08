<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

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

    public function update(Request $request) {
        $token = $request->header('Authorization');
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        //1. Recoger los datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);//Array

        if($checkToken && !empty($params_array)) {
            //2. Sacar usuario identificado
            $user = $jwtAuth->checkToken($token, true);

            //3. Validar datos
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($user->sub)
                ]
            ]);
            //Nota:
            //Al concatenear el 'sub' (llave primaria ó ID) se valida que el email no exista
            //en registros diferentes al que se esta actualizando.

            //4. Quitar los campos que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

            if($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no existe en el sistema',
                    'errors' => $validate->errors()
                );
            } else {
                //5. Actualizar usuario en base de datos
                User::where('id', $user->sub)->update($params_array);
                //5. Devolver array con resultado
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha actualizado correctamente',
                    'user' => $user,
                    'changes' => $params_array,
                );
            }

        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no está identificado'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request) {
        //1. Recoger los datos de la petición
        $image = $request->file('file0');

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpeg,jpg,bmp,png',
        ]);

        //2. Guardar la imagen
        if(!$image || $validate->fails()) {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Error al subir imagen'
            );
        } else {
            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));

            //3. Devolver el resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );
        }

        return response()->json($data, $data['code']);
    }

    public function getImage($filename) {
        $isset = \Storage::disk('users')->exists($filename);

        if($isset) {
            $file = \Storage::disk('users')->get($filename);

            return new Response($file, 200);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'La imagen no existe'
            );

            return response()->json($data, $data['code']);
        }

    }

    public function detail($id) {
        $user = User::find($id);

        if(is_object(($user))) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no existe'
            );
        }

        return response()->json($data, $data['code']);
    }
}
