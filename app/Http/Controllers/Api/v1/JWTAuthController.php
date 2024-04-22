<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\{AuthUserRequest, StoreUserRequest};
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, Hash};

class JWTAuthController extends Controller
{
    public function register(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = User::create([...$request->validated(), 'password' => Hash::make($request->password)]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }

        return response()->json($user, 201);
    }

    public function login(AuthUserRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    public function profile(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json(['message' => 'Success']);
    }
}
