<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Http\Requests\UpdateUserRequest;

class AuthController extends Controller
{
    public function register(UserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json(['success' => true, 'message' => 'User registered successfully'], 201);
        } catch (Exception $e) {
            Log::info('Register Error: ' . $e->getMessage());
            return response()->json(['success' => false,'error' => 'Something went wrong'], 500);
        }
    }

    public function login(LoginUserRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json(['success' => false, 'error' => 'Invalid credentials'], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['success' => true, 'message' => 'Login successful', 'token' => $token], 200);
        } catch (Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return response()->json(['success' => false,'error' => 'Something went wrong'], 500);
        }
    }

    public function index()
    {
        try {
            $users = User::all()->whereNull('deleted_at');
            return response()->json(['success' => true,'users' => $users], 200);
        } catch (Exception $e) {
            Log::info('Fetch Users Error: ' . $e->getMessage());
            return response()->json(['success' => false,'error' => 'Something went wrong'], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user || $user->deleted_at) {
                return response()->json(['success' => false,'error' => 'User not found'], 404);
            }

            return response()->json(['success' => true,'user' => $user], 200);
        } catch (Exception $e) {
            Log::info('Fetch User Error: ' . $e->getMessage());
            return response()->json(['success' => false,'error' => 'Something went wrong'], 500);
        }
    }

    public function update(UpdateUserRequest $request, $id)
{
    try {
        $user = User::find($id);

        if (!$user || $user->deleted_at) {
            return response()->json(['success' => false,'error' => 'User not found'], 404);
        }

        $user->update($request->only('name', 'email'));

        return response()->json(['success' => true, 'message' => 'User updated successfully', 'user' => $user], 200);
    } catch (Exception $e) {
        Log::info('Update User Error: ' . $e->getMessage());
        return response()->json(['success' => false,'error' => 'Something went wrong'], 500);
    }
}

}
