<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profileImage = $this->getProfileImageUrl($user->username);
        
        return view('dashboard', [
            'profileImage' => $profileImage,
        ]);
    }

    public function update_photo(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        // Hapus foto lama (kalau ada)
        if ($user->photo && file_exists(public_path('img/' . $user->photo))) {
            unlink(public_path('img/' . $user->photo));
        }

        // Simpan foto baru
        $file = $request->file('profile_photo');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('img'), $filename); // <- simpan langsung ke public/img

        $user->photo = $filename;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui.',
            'image_url' => asset('img/' . $filename)
        ]);
    }
}