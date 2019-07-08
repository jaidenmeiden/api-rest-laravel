<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth {

    public function singup(){
        //1. Buscar si existe el suario con sus credenciales
        //2. Comprobar si son correctas(objeto)
        //3. Generar el tocken con los datos del usuario identificado
        //4. Devolver los datos decodificados o el objeto token, en gunión de un parámetro

        return 'Método de la clase JwtAuth';
    }

}