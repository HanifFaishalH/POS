<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // GET /users
    public function index()
    {
        return response()->json(UserModel::with('level')->get());
    }

    // POST /users
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:m_user,username',
            'nama' => 'required|string|max:100',
            'password' => 'required|min:6',
            'level_id' => 'required|exists:m_level,level_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Simpan user
        $user = UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => bcrypt($request->password),
            'level_id' => $request->level_id,
        ]);

        return response()->json($user, 201);
    }

    // GET /users/{user}
    public function show(UserModel $user)
    {
        return response()->json($user->load('level'));
    }

    // PUT /users/{user}
    public function update(Request $request, UserModel $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|required|unique:m_user,username,' . $user->user_id . ',user_id',
            'nama' => 'sometimes|required|string|max:100',
            'password' => 'nullable|min:6',
            'level_id' => 'sometimes|required|exists:m_level,level_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Jika password diisi, hash lagi
        if ($request->filled('password')) {
            $request->merge([
                'password' => bcrypt($request->password)
            ]);
        } else {
            $request->request->remove('password');
        }

        $user->update($request->all());

        return response()->json($user->load('level'));
    }

    // DELETE /users/{user}
    public function destroy(UserModel $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
