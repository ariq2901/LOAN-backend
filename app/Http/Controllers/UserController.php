<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use SMTPValidateEmail\Validator as SmtpEmailValidator;

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
        if($request->role == 'teacher' || $request->role == 'musyrif') {
            if($request->parent_email != null) {
               unset($request['parent_email']);
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
                'role' => 'required'
            ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email',
                    'password' => 'required',
                    'c_password' => 'required|same:password',
                    'role' => 'required',
                    'parent_email' => 'required'
                ]);
                $smtpEmail = $this->checkEmail($request->parent_email, $request->role);
                if($smtpEmail != true) {
                return response()->json(["error" => "there is no email with that name"], 401);
            }
        }
        if($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        if($input['role'] == 'teacher' || $input['role'] == 'guru') {
            $user->assignRole('teacher');
        }
        if($input['role'] == 'student' || $input['role'] == 'murid') {
            $user->assignRole('student');
        }
        if($input['role'] == 'musyrif') {
            $user->assignRole('musyrif');
        }
        
        
        event(new Registered($user));
        $data['message'] = "Please verify your email that we've sent to your mailbox";
        $data['name'] = $user->name;
        $data['role'] = $user->getRoleNames();
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
        $user['role'] = $user->getRoleNames();
        return response()->json(['data' => $user], $this->successCode);
    }

    public function checkEmail($parent_email, $role) {
        if($parent_email == null && $role == "student") {
            return response()->json(["parent_email is required"], 401);
        }
        $email = $parent_email;
        $sender = "fakeghuroba@gmail.com";
        $validator = new SmtpEmailValidator($email, $sender);
        $validasi = $validator->validate();
        $result = array_values($validasi);
        $result = $result[0];
        if($result != true) {
            return false;
        }
        return true;
    }
}
