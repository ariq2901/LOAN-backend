<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\VerifiesEmails;

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
        if($request->user()->hasVerifiedEmail()) {
            return response()->json("User already have verified email!", 422);
        }
        $request->user()->sendEmailVerificationNotification();
        return response()->json("The notification has been sent to your mailbox!");
    }
}
