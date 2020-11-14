<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $successCode = 200;

    public function login() {
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $data['token'] = $user->createToken('nApp')->accessToken;
            return response()->json(['data' => $data], $this->successCode);
        } else {
            return response()->json(['error' => 'Unathorized'], 400);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'role' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        if($input['role'] == 'guru') {
            $user->assignRole('guru');
        }
        if($input['role'] == 'murid') {
            $user->assignRole('murid');
        }
        
        event(new Registered($user));
        $data['name'] = $user->name;
        $data['token'] = $user->createToken('nApp')->accessToken;
        return response()->json(['data' => $data], $this->successCode);
    }

    public function logout(Request $request) {
        $logout = $request->user()->token()->revoke();
        if($logout) {
            return response()->json(['message' => 'successfully logged out']);
        } else {
            return response()->json(['error' => 'there is no token in authorization / token has been expired'], 401);
        }
    }
    
    public function detailUser() {
        $user = Auth::user();
        if($user->hasRole('guru')) {
            return response()->json('ini guru', $this->successCode);
        }

        return response()->json('ini murid', $this->successCode);
    }
}
