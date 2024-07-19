<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Http\Requests\UpdateUserRequest;
use Carbon\Carbon;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
    public function register(UserRequest $request)
    {
        try {
            $otp = rand(100000, 999999);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new OTPMail($otp));

            return response()->json(['success' => true, 'message' => 'Verification OTP sent'], 201);
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

            if (is_null($user->email_verified_at)) {
            return response()->json(['success' => false, 'error' => 'Email not verified'], 403);
        }
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

public function destroy($id)
{
    try {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted successfully'], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['success' => false, 'error' => 'User not found'], 404);
    } catch (Exception $e) {
        Log::info('Delete User Error: ' . $e->getMessage());
        return response()->json(['error' => 'Something went wrong'], 500);
    }
}

public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|numeric',
    ]);

    try {
        $user = User::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->where('otp_expires_at', '>', Carbon::now())
                    ->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid or expired OTP'], 401);
        }

        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'email_verified_at' => Carbon::now(), 
        ]);

        return response()->json(['success' => true, 'message' => 'OTP verified successfully'], 200);
    } catch (Exception $e) {
        Log::info('OTP Verification Error: ' . $e->getMessage());
        return response()->json(['error' => 'Something went wrong'], 500);
    }
}

}
