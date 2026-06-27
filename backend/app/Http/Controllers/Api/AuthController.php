<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        $key = 'admin-login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Too many login attempts. Try again in {$seconds} seconds.",
            ], 429);
        }

        $admin = User::where('email', $request->email)
                     ->where('role', 'admin')
                     ->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            RateLimiter::hit($key, 300);
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        RateLimiter::clear($key);

        $token = $admin->createToken('admin-token', ['admin'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'    => $admin->id,
                'name'  => $admin->name,
                'email' => $admin->email,
                'role'  => $admin->role,
            ],
        ]);
    }

    public function clientLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        $key = 'client-login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Too many login attempts. Try again in {$seconds} seconds.",
            ], 429);
        }

        $client = User::where('email', $request->email)
                      ->where('role', 'client')
                      ->first();

        if (! $client || ! Hash::check($request->password, $client->password)) {
            RateLimiter::hit($key, 300);
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if ($client->status !== 'approved') {
            $msg = $client->status === 'pending'
                ? 'Your account is pending admin approval. We will notify you by email.'
                : 'Your account has been rejected. Contact us for more information.';
            return response()->json(['message' => $msg], 403);
        }

        RateLimiter::clear($key);

        $token = $client->createToken('client-token', ['client'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'     => $client->id,
                'name'   => $client->name,
                'email'  => $client->email,
                'phone'  => $client->phone,
                'role'   => $client->role,
                'status' => $client->status,
            ],
        ]);
    }

    public function clientRegister(Request $request)
    {
        $key = 'register:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Too many registrations. Try again in {$seconds} seconds.",
            ], 429);
        }

        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|max:255|unique:users,email',
            'phone'     => 'nullable|string|max:20',
            'password'  => 'required|string|min:8|confirmed',
            'interests' => 'nullable|string|max:1000',
        ]);

        RateLimiter::hit($key, 3600);

        $client = User::create([
            'name'      => strip_tags($request->name),
            'email'     => $request->email,
            'phone'     => $request->phone ? preg_replace('/[^0-9+\-\s()]/', '', $request->phone) : null,
            'password'  => Hash::make($request->password),
            'role'      => 'client',
            'status'    => 'pending',
            'interests' => $request->interests ? strip_tags($request->interests) : null,
        ]);

        return response()->json([
            'message' => 'Registration successful. Awaiting admin approval.',
            'user'    => ['id' => $client->id, 'name' => $client->name, 'email' => $client->email],
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'phone'       => $user->phone,
            'role'        => $user->role,
            'status'      => $user->status,
            'interests'   => $user->interests,
            'approved_at' => $user->approved_at,
            'created_at'  => $user->created_at,
        ]);
    }
}
