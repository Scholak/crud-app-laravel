<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): Response
    {
        $data = $request->validated();
        $data['password'] = bcrypt($request->password);

        return User::create($data) ? response(['message' => 'Registered successfully'], Response::HTTP_CREATED) : response(['message' => 'Failed to register'], Response::HTTP_BAD_REQUEST);
    }

    public function login(LoginRequest $request): Response
    {
        $user = User::whereEmail($request->email)->first();

        if (!$user) {
            return response(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response(['message' => 'Wrong password'], Response::HTTP_BAD_REQUEST);
        }

        $token = $user->createToken($request->email)->plainTextToken;

        $cookie = cookie('jwt', $token, 60);

        return response(['message' => 'Logged in successfully'], Response::HTTP_OK)->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $cookie = Cookie::forget('jwt');
        $request->user()->currentAccessToken()->delete();

        return response([], Response::HTTP_NO_CONTENT)->withCookie($cookie);
    }

    public function profile(Request $request)
    {
        return response(['message' => 'Profile fetched successfully.', 'profile' => $request->user()], Response::HTTP_OK);
    }
}
