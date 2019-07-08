<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::all();

        return \response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        ]);
    }

    public function show($id) {
        $category = Category::find($id);

        if(is_object($category)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'category' => $category
            );
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'La categoría no existe'
            );
        }

        return \response()->json($data, $data['code']);
    }
}
