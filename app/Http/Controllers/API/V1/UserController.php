<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Retrieves users and returns a JSON response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $users = User::select('id as userId', 'email', 'name as firstName', 'last_name as lastName')
            ->paginate(20);
        return response()->json($users);
    }

    /**
     * Register and returns a JSON response.
     *
     * @param UserRegisterRequest $request description
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validated();

        User::create([
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'name' => $validatedData['name'],
            'last_name' => $validatedData['last_name'],
        ]);

        return response()->json(['message' => 'User registered successfully']);
    }

    /**
     * Login and returns a JSON response.
     *
     * @param UserLoginRequest $request description
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $accessToken = Auth::user()->createToken('authToken')->plainTextToken;
            return response()->json(['access_token' => $accessToken]);
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
