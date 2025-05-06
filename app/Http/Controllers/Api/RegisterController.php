<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'nama' => 'required|string|max:255',
            'level_id' => 'required|integer|exists:m_level,level_id',
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Buat direktori jika belum ada
        if (!File::exists(public_path('img'))) {
            File::makeDirectory(public_path('img'), 0755, true);
        }

        // Simpan gambar ke public/img
        $image = $request->file('profile_photo');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('img'), $imageName);

        // Buat pengguna baru
        $user = UserModel::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'nama' => $request->nama,
            'level_id' => $request->level_id,
            'photo' => $imageName, // Menyimpan nama file foto di kolom 'photo'
        ]);

        if ($user) {
            return response()->json([
                'success' => true,
                'user' => $user,
                'photo_url' => asset('img/' . $user->photo), // Pastikan URL foto berisi nama file yang benar
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat mendaftar pengguna.',
        ], 409);
    }

}