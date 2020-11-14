<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:guru']);
    }
    
    public function hello() {
        return response()->json("Hello, you're in Guru Controller", 200);
    }
    public function laporan() {
        return response()->json("disini adalah method laporan", 200);
    }
}
