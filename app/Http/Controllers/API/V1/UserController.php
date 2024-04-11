<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
     * @param Request $request description
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'name' => 'required',
            'last_name' => 'required',
        ]);

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
     * @param Request $request description
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $accessToken = Auth::user()->createToken('authToken')->plainTextToken;
            return response()->json(['access_token' => $accessToken]);
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
