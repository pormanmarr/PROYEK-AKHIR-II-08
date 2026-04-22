<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);

            // Try to find by email first (assuming email is username in your system)
            // If not found, try username field
            $akun = Akun::where('username', $validated['email'])
                ->orWhere('id_akun', $validated['email'])
                ->first();

            if (!$akun || !Hash::check($validated['password'], $akun->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email/Username atau password salah',
                ], 401);
            }

            // Generate a simple token (can be replaced with Sanctum if needed)
            $token = bin2hex(random_bytes(32));

            // Get nama from siswa or guru
            $nama = 'User';
            if ($akun->nomor_induk_siswa && $akun->siswa) {
                $nama = $akun->siswa->nama_siswa ?? 'User';
            } elseif ($akun->id_guru && $akun->guru) {
                $nama = $akun->guru->nama_guru ?? 'User';
            }

            // Return user data
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => [
                    'id_akun' => $akun->id_akun,
                    'username' => $akun->username,
                    'role' => $akun->role,
                    'id_guru' => $akun->id_guru,
                    'nomor_induk_siswa' => $akun->nomor_induk_siswa,
                    'is_super_admin' => $akun->is_super_admin,
                    'nama_siswa' => $nama,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
