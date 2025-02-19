<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeCareController extends Controller
{
    public function homecare() {
        return view('home-care');
    }
}
