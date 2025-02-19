<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BeautynHealthController extends Controller
{
    public function bnh() {
        return view('beauty-healthy');
    }
}
