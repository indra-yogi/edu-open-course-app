<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'redirect' => $user->role === 'admin' ? route('admin.dashboard') : route('user.dashboard'),
                'user' => [
                    'name' => $user->name,
                    'role' => $user->role,
                    'email' => $user->email,
                ],
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials.',
        ], 401);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // uses password_confirmation field
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user', // default role
        ]);

        Auth::login($user); // auto-login after registration

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful.',
            'redirect' => route('user.dashboard'),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

}
