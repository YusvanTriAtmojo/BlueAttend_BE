<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email tidak ditemukan',
                'status_code' => 404,
                'data' => null
            ], 404);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Password salah',
                'status_code' => 401,
                'data' => null
            ], 401);
        }

        if (!$token = Auth::guard('api')->login($user)) {
            return response()->json([
                'message' => 'Gagal membuat token',
                'status_code' => 500,
                'data' => null
            ], 500);
        }

        $formatedUser = [
            'id'    => $user->id,
            'nama'  => $user->nama,
            'nip'  => $user->nip,
            'notlp'  => $user->notlp,
            'alamat'  => $user->alamat,
            'email' => $user->email,
            'role'  => $user->role->nama_role,
            'token' => $token,
            'foto_profile' => $user->foto_profile ? asset('storage/' . $user->foto_profile) : null,
        ];

        return response()->json([
            'message' => 'Login berhasil',
            'status_code' => 200,
            'data' => $formatedUser
        ], 200);
    }

    public function me()
    {
        try {
            $user = Auth::guard('api')->user();
            $user->load('role');

            return response()->json([
                'message'     => 'User ditemukan',
                'status_code' => 200,
                'data'        => [
                    'id'    => $user->id,
                    'nama'  => $user->nama,
                    'nip'  => $user->nip,
                    'notlp'  => $user->notlp,
                    'alamat'  => $user->alamat,
                    'email' => $user->email,
                    'role'  => $user->role ? $user->role->nama_role : null,
                    'foto_profile' => $user->foto_profile ? asset('storage/' . $item->profile) : null,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message'     => $e->getMessage(),
                'status_code' => 500,
                'data'        => null
            ], 500);
        }
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message'     => 'Logout berhasil',
            'status_code' => 200,
            'data'        => null
        ], 200);
    }
}
