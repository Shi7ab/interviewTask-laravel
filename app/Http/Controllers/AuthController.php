<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $user = $this->authService->register($request->all());

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $result = $this->authService->login($request->all());

        if (!$result) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $result['token'],
            'user' => $result['user'],
            'token_type' => 'Bearer'
        ]);
    }

    public function logout()
    {
        $this->authService->logout();

        return response()->json(['message' => 'Logout successful']);
    }
}
