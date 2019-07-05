<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PruebasController extends Controller
{
    public function index()
    {
        $titulo = 'Animales';
        $animales = ['León', 'Leopardo', 'Tigre', 'Chita', 'Lince', 'Pantera'];

        return view('pruebas.index', array(
            'titulo' => $titulo,
            'animales' => $animales
        ));
    }

    public function getPosts()
    {
        $posts = Post::all();
        foreach ($posts as $post){
            echo "<h1>" . $post->title . "</h1>";
            echo "<span style='background-color: #5cd08d'>{$post->user->name} - {$post->category->name}</span>";
            echo "<p>" . $post->content . "</p>";
            echo "</hr>";
        }

        die();
    }
    public function getCategories()
    {
        $categories = Category::all();
        foreach ($categories as $category) {
            echo "<h1>" . $category->name . "</h1>";

            foreach ($category->posts as $post) {
                echo "<h3>" . $post->title . "</h3>";
                echo "<span style='background-color: #5cd08d'>{$post->user->name}</span>";
                echo "<p>" . $post->content . "</p>";
            }

            echo "</hr>";
        }

        die();
    }
}
