<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\User;
use App\Http\Requests\ValidationRole;
use App\Notifications\EmailNotification;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    Public function register(Request $request, validationRole $validationRole)
    {
       /* $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);*/
        $validatedData = $request->validate((new validationRole())->rules());

        $user = \App\Models\User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

       // $user->notify(new EmailNotification($user->email));
        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
            'token_type' => 'Bearer'
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logout successful'], 200);
    }
}
