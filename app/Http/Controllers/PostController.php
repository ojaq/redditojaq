<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(){
        $posts = Post::all();
        return PostResource::collection($posts);
    }

    public function show($id){
        try {
        $post = Post::with('writer:id,username')->findOrFail($id);
        return new PostDetailResource($post);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Post not found'], 404);
        }
    } 
}