<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(){
        // $posts = Post::all();
        // return PostResource::collection($posts->loadMissing('redditor:id,username'));
        $posts = Post::with(['redditor:id,username', 'votes'])->get();
        return PostResource::collection($posts);
    }

    public function show($id){
        try {
        $post = Post::with('redditor:id,username', 'comments:id,post_id,user_id,comments_content', 'votes')->findOrFail($id);
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
            $extensionfr = ['jpg', 'jpeg', 'png'];
            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();
            $image = $fileName. '.' .$extension;

            if(!in_array($extension, $extensionfr)){
                return response()->json([
                    "message" => "only image are allowed"
                ]);
            }
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
            $extensionfr = ['jpg', 'jpeg', 'png'];
            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();
            $image = $fileName. '.' .$extension;

            if(!in_array($extension, $extensionfr)){
                return response()->json([
                    "message" => "only image are allowed"
                ]);
            }
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

    public function search(Request $request){
        $search = $request->input('q');

        $posts = Post::where('post_title', 'like', '%'.$search.'%')
                    ->orWhere('content', 'like', '%'.$search.'%')
                    ->get();

    if ($posts->isEmpty()) {
        return response()->json(['error' => 'no posts found'], 404);
    }

    return response()->json($posts);
    }

    public function upvote(Request $request, $id)
    {
        $user = Auth::user();
        $post = Post::findOrFail($id);
    
        if ($post->votes()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'you already upvoted this post.'], 409);
        }
    
        $upvote = new Vote();
        $upvote->user_id = $user->id;
        $upvote->post_id = $post->id;
        $upvote->save();
    
        return response()->json(['message' => 'post upvoted.']);
    }
    
    public function unvote(Request $request, $id)
    {
        $user = Auth::user();
        $post = Post::findOrFail($id);
    
        $unvote = $post->votes()->where('user_id', $user->id)->first();
    
        if (!$unvote) {
            return response()->json(['message' => 'you have not unvoted this post.'], 409);
        }
    
        $unvote->delete();
        return response()->json(['message' => 'post unvoted.']);
    }
    
}