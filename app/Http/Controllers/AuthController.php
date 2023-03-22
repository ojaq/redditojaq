<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);
    
        $user = User::where(function ($query) use ($request) {
                $query->where('email', $request->login)
                      ->orWhere('username', $request->login);
            })
            ->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'account' => ['The provided credentials are incorrect'],
            ]);
        }
    
        return $user->createToken('logged in!')->plainTextToken;
    }    

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'logged out!'
        ]);
    }
}