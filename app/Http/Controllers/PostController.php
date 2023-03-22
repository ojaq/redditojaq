<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(){
        $posts = Post::all();
        return PostResource::collection($posts);
    }

    public function show($id){
        try {
        $post = Post::with('redditor:id,username')->findOrFail($id);
        return new PostDetailResource($post);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Post not found'], 404);
        }
    } 

    public function store(Request $request){
        $request ->validate([
            'post_title' => 'required|max:255',
            'content' => 'required',
        ]);
        $request['redditor'] = Auth::user()->id;
        $post = Post::create($request->all());
        return new PostDetailResource($post->loadMissing('redditor:id,username'));
    }
    
    public function update(Request $request, $id){
        $request -> validate([
            'post_title' => 'required|max:255',
            'content' => 'required',
        ]);
        $post = Post::findOrFail($id);
        $post->update($request->all());

        return new PostDetailResource($post->loadMissing('redditor:id,username'));
    }

    public function delete($id){
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => "deleted!"
        ]);
    }
}