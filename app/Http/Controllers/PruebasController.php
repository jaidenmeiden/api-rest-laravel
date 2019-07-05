<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PruebasController extends Controller
{
    public function index() {
        $titulo = 'Animales';
        $animales = ['León', 'Leopardo', 'Tigre', 'Chita', 'Lince', 'Pantera'];

        return view('pruebas.index', array(
            'titulo' => $titulo,
            'animales' => $animales
        ));
    }
}
