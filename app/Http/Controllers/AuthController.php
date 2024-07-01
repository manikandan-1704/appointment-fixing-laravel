<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController extends Controller
{
    public function register(UserRequest $request)
    {
        try{

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);

        }catch(Exception $e){
            
        Log::info('Register Error: '.$e->getMessage());
        return response()->json(['error' => 'Something went wrong'], 500);
    }

    }
}
