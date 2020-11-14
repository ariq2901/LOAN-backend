<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\Auth;

class VerificationApiController extends Controller
{
    use VerifiesEmails;

    /**
    * Mark the authenticated user’s email address as verified.
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function verify(Request $request)
    {
        $userID = $request['id'];
        $user = User::findOrFail($userID);
        $date = date('Y-m-d g:i:s');
        $user->email_verified_at = $date;
        $user->save();
        return view('emailverified');
    }
    public function resend(Request $request)
    {
        $userEmail = $request['email'];
        $user = User::where("email", $userEmail)->first();
        if(!$user) {
            return response()->json("There is no user with that email in our data", 401);
        } elseif ($user['email_verified_at'] != null) {
            return response()->json("The user email has been verified", 401);
        }
        $user->sendEmailVerificationNotification();
        return response()->json("The notification has been sent to your mailbox!", 200);
    }
}
