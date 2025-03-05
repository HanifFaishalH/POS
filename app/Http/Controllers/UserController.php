<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\returnSelf;

class UserController extends Controller
{
   public function index() {
        
        $user = UserModel::firstOrNew(
            [   
                // 'username'  => 'manager',
                // 'nama'  => 'Manager',

                'username'  => 'manager33',
                'nama'  => 'Manager Tiga Tiga',
                'password'  => Hash::make('12345'),
                'level_id'  => 2
            ]
        );
        $user->save();

        return view('user', ['data' => $user]);
    }
}
