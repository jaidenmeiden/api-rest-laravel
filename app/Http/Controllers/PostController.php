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
        $this->middleware('api.auth', ['except' => [
            'index',
            'show',
            'getImage',
            'getPostsByCategory',
            'getPostsByUser'
        ]]);
    }


    public function index() {
        $posts = Post::all()->load('category');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    public function show($id) {
        $post = Post::find($id)->load('category');

        if(is_object($post)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'post' => $post
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
            $user = $this->getIndentity($request);

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
                //5. Guardar el post
                $post = new Post();
                $post->title = $params_array['title'];
                $post->content = $params_array['content'];
                $post->category_id = $params_array['category_id'];
                $post->user_id = $user->sub;
                $post->image = $params->image;
                $post->save();

                //6. Devolver resultado
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
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

    public function update($id, Request $request) {
        //1. Recoger los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);//Array

        $data = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'No has enviado ningún post'
        );

        if(!empty($params_array)) {
            //2. Validar los datos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required',
            ]);

            //3. Quitar lo que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            if($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            } else {
                //4. Conseguir usuario identificado
                $user = $this->getIndentity($request);

                //5. Conseguir registro
                $post = Post::where('id', $id)
                    ->where('user_id', $user->sub)
                    ->first();

                if(is_object($post)) {
                    //6. Actualizar
                    $post->update($params_array);

                    //7. Devolver resultado
                    $data = array(
                        'code' => 200,
                        'status' => 'success',
                        'post' => $post,
                        'changes' => $params_array
                    );
                } else {
                    //7. Devolver resultado
                    $data['message'] = 'No tiene permisos para actualizar el registro';
                }
            }
        }

        return \response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request) {
        //1. Conseguir usuario identificado
        $user = $this->getIndentity($request);

        //2. Conseguir registro
        $post = Post::where('id', $id)
            ->where('user_id', $user->sub)
            ->first();

        if(is_object($post)) {
            //3. Borrarlo
            $post->delete();

            //4. Devolver algo
            $data = array(
                'code' => 200,
                'status' => 'success',
                'post' => $post
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

    private function getIndentity(Request $request) {
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        return $jwtAuth->checkToken($token, true);
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
            \Storage::disk('images')->put($image_name, \File::get($image));

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
        $isset = \Storage::disk('images')->exists($filename);

        if($isset) {
            $file = \Storage::disk('images')->get($filename);

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

    public function getPostsByCategory($id) {
        $posts = Post::where('category_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    public function getPostsByUser($id) {
        $posts = Post::where('user_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }
}
