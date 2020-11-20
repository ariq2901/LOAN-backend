<?php

namespace App\Http\Controllers;

use App\Models\Avtar;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use SMTPValidateEmail\Validator as SmtpEmailValidator;
use Laravolt\Avatar\Facade as Avatar;

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
        $userId = $user->id;
        if($input['role'] == 'teacher' || $input['role'] == 'guru') {
            $user->assignRole('teacher');
        }
        if($input['role'] == 'student' || $input['role'] == 'murid') {
            $user->assignRole('student');
        }
        if($input['role'] == 'musyrif') {
            $user->assignRole('musyrif');
        }
        $picName = md5($input['name']) . '.' . 'png';
        Avatar::create($input['name'])->save(public_path('/storage/users/') . $picName, $quality = 90);
        $avtar = [];
        $avtar['user_id'] = $userId;
        $avtar['image'] = $picName;
        Avtar::create($avtar);
        
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
        if($user['email_verified_at'] == null) {
            return response()->json(['error' => "verify your account to see user detail"], 400);
        }
        $user['avatar'] = $user->avtar->image;
        $user['role'] = $user->getRoleNames();
        unset($user['roles']);
        unset($user['avtar']);
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

    public function getUsersByRole($role) {
        $roles = Role::where('name', $role)->get();
        $users = User::role($role)->get();
        if(!$roles) {
            return response()->json(["error" => "There is no role with name " . $role], 404);
        }
        return response()->json(["user" => $users], 200);
    }

    public function imageDownload()
    {
        return response()->download(public_path('storage/users/14f82ac0a5301331c9bcaeb36b5cd02b.png'), 'Postman image ss');
    }
}
