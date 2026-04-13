<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function editPassword()
    {
        $akun = Akun::find(session('akun_id'));
        return view('profile.edit-password', compact('akun'));
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:8|confirmed',
        ], [
            'password_lama.required' => 'Password lama harus diisi',
            'password_baru.required' => 'Password baru harus diisi',
            'password_baru.min' => 'Password baru minimal 8 karakter',
            'password_baru.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        $akun = Akun::find(session('akun_id'));

        // Cek password lama
        if (!Hash::check($validated['password_lama'], $akun->password)) {
            return back()->withErrors([
                'password_lama' => 'Password lama tidak sesuai',
            ])->onlyInput('password_lama');
        }

        // Update password baru
        $akun->update([
            'password' => Hash::make($validated['password_baru']),
        ]);

        return redirect()->route('dashboard')->with('success', 'Password berhasil diubah!');
    }
}
