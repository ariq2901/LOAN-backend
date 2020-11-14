<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:teacher']);
    }
    
    public function hello() {
        return response()->json("Hello, you're in Teacher Controller", 200);
    }
    public function laporan() {
        return response()->json("You're in laporan method", 200);
    }
}
