<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth {

    public $key;

    public function __construct()
    {
        $this->key = '7q4RBo72dNAIG0540UiRXKXIhmUkmBh2';
    }

    public function singup($email, $password, $getToken = null){
        //1. Buscar si existe el suario con sus credenciales
        $user = User::where ([
            'email' => $email,
            'password' => $password
        ])->first();

        //2. Comprobar si son correctas(objeto)
        $signup = false;
        if(is_object($user)) {
            $signup = !$signup;
        }

        //3. Generar el token con los datos del usuario identificado
        if($signup) {
            $token = array(
                'sub' => $user->id, //La propiedad 'sub' hace referencia al ID en la base de datos
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(), //El tiempo en que fue creado el token
                    'exp' => time() + (7 * 24 * 60 * 60) //Cuando expira el token (Una semana)
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');

            if(is_null($getToken)) {
                $data = $jwt;
            } else {
                $decoded = JWT::decode($jwt, $this->key, ['HS256']);
                $data = $decoded;
            }
        } else {
            $data = array(
                'status' => 'error',
                'message' => 'Login incorrecto'
            );
        }

        //4. Devolver los datos decodificados o el objeto token, en función de un parámetro

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false) {
        $auth = false;

        try {
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

            if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
                $auth = !$auth;
            } else {
                $auth = false;
            }

            if($getIdentity) {
                return $decoded;
            }
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        return $auth;
    }

}