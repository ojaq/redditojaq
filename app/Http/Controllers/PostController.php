<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(){
        $posts = Post::all();
        return PostDetailResource::collection($posts->loadMissing('redditor:id,username', 'comments:id,post_id,user_id,comments_content'));
    }

    public function show($id){
        try {
        $post = Post::with('redditor:id,username', 'comments:id,post_id,user_id,comments_content')->findOrFail($id);
        return new PostDetailResource($post);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'post not found'], 404);
        }
    } 

    public function store(Request $request){
        $request ->validate([
            'post_title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable',
        ]);

        $image = null;
        if ($request -> file) {
            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();

            $image = $fileName. '.' .$extension;
            Storage::putFileAs('image', $request->file, $image);
        }
        $request['image'] = $image;

        $request['redditor'] = Auth::user()->id;
        $post = Post::create($request->all());
        return new PostDetailResource($post->loadMissing('redditor:id,username'));
    }
    
    public function update(Request $request, $id){
        $request -> validate([
            'post_title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable',
        ]);

        $image = null;
        if ($request -> file) {
            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();

            $image = $fileName. '.' .$extension;
            Storage::putFileAs('image', $request->file, $image);
        }
        $request['image'] = $image;

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

    function generateRandomString($length =30) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function search(Request $request)
{
    $searchQuery = $request->input('q');

    $posts = Post::where('post_title', '%'.$searchQuery.'%')
                 ->orWhere('content', '%'.$searchQuery.'%')
                 ->get();

    if ($posts->isEmpty()) {
        return response()->json(['error' => 'No posts found'], 404);
    }

    return response()->json($posts);
}
}