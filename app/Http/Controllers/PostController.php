<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }


    public function index() {
        $posts = Post::all()->load('category');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $posts
        ], 200);
    }

    public function show($id) {
        $post = Post::find($id)->load('category');

        if(is_object($post)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'category' => $post
            );
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El post no existe'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
        //1. Recoger los datos
        $json = $request->input('json', null);
        $params = json_decode($json);//Object
        $params_array = json_decode($json, true);//Array

        if(!empty($params_array)) {
            //2. Conseguir usuario identificado
            $jwtAuth = new JwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtAuth->checkToken($token, true);

            //3. Validar los datos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required',
            ]);

            //4. Validar la contraseña
            if($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'No se ha guardado el post'
                );
            } else {
                //3. Guardar el post
                $post = new Post();
                $post->title = $params_array['title'];
                $post->content = $params_array['content'];
                $post->category_id = $params_array['category_id'];
                $post->user_id = $user->sub;
                $post->image = $params->image;
                $post->save();

                //4. Devolver resultado
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'category' => $post
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No has enviado ningún post'
            );
        }

        return \response()->json($data, $data['code']);
    }

}
