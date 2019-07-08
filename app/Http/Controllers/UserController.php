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
         * Pasos:
         * Un API RESTful debe devolver un JSON, si no es así, es un servicio web normal
         *
         * 1. Recoger los datos del uusario por post
         * 2. Validar datos
         * 3. Cifrar la contraseña
         * 4. Comprobar si el suario existe ya (Duplicado)
         * 5. Crear el usuario
         */
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
