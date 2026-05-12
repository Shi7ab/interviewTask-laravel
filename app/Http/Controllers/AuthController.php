<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\User;
use Http\Requests\validationRole;

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

        return response()->json(['message' => 'User registered successfully'], 201);
    }
    public function login(Request $request, validationRole $validationRole)
    {

        try{

        $credentials = $request->validate((new validationRole())->rules());
        $token = $request->user()->createToken('auth_token')->plainTextToken;
       if (auth()->attempt($credentials)) {
            return response()->json(['message' => 'Login successful', 'token' => $token], 200);
        }

            return response()->json(['message' => 'Invalid credentials'], 401);
        }catch(\Exception $e){
            return response()->json(['message' => 'An error occurred during login', 'error' => $e->getMessage()], 500);
        }
    }
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logout successful'], 200);
    }
}
