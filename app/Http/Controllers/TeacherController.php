<?php

namespace App\Http\Controllers;

use App\Mail\PeminjamanEmail;
use App\Models\Assignment;
use App\Models\Borrowing;
use App\Models\Crucial;
use App\Models\User;
use App\StatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    
    public function hello()
    {
        return response()->json("Hello, you're in Teacher Controller", 200);
    }

    public function laporan()
    {
        return response()->json("You're in laporan method", 200);
    }

    public function listApproval($approver)
    {
        if($approver == 'teacher') {
            $borrow = Borrowing::where("urgent", false)->where("approved", null)->get();
        } else {
            $borrow = Borrowing::where("urgent", true)->where("approved", null)->get();
        }

        return response()->json(["list approval" => $approver,"data" => $borrow], 200);
    }

    public function showApproval($id)
    {
        $borrow = Borrowing::find($id)->first();
        return response()->json(["message" => "success", "data" => $borrow], 200);
    }

    public function approvement($id, Request $request)
    {
        if(Borrowing::where("id", $id)->first() == null) {
            return response()->json(["error" => "there is no borrowing request with id " . $id], 404);
        }
        if($request->approved == true) {
            $validator = Validator::make($request->all(), [
                "approved" => "required",
                "teacher_reason" => "required"
            ]);
            if($validator->fails()) {
                return response()->json(["error" => $validator->errors()], 401);
            }
            $input = $request->all();
            Borrowing::where("id", $id)
                        ->update([
                                'approved' => $input['approved'],
                                'teacher_reason' => $input['teacher_reason']
                            ]);
                                
            return response()->json(["message" => "borrowing request has been accepted"], 200);
        }

        $validator = Validator::make($request->all(), [
            "approved" => "required",
            "teacher_reason" => "required"
        ]);
        if($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 401);
        }
        $input = $request->all();
        Borrowing::where("id", $id)
                    ->update([
                            'approved' => $input['approved'],
                            'teacher_reason' => $input['teacher_reason']
                        ]);
        return response()->json(["message" => "borrowing request has been declined"], 401);
    }

    public function showAssignment($id)
    {
        $label = Assignment::where("id", $id);
        $assignment = $label->first();
        // $assignment['picture'] = $label->picture;
        $assignment['picture'] = Assignment::where("id", $id)->first()->picture[0]['image'];
        if($assignment == null) {
            return response()->json(["error" => "there is no assignment submission"], 404);
        }
        return response()->json(["setor tugas" => $assignment], 200);
        //? to download image
        // return response()->download(public_path('storage/users/'.$assignment->image), 'image view');
    }

    public function pass(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required'
        ]);
        if($validator->fails()) {
            return response()->json(["error" => $validator->errors()], StatusCode::BAD_REQUEST);
        }
        
        $input = $request->all();
        $input['assignment_id'] = $id;
        $assignment_id = $input['assignment_id'];
        $crucial = Crucial::create($input);
        $crucial_id = $crucial->id;
        $penalty = $this->penalty($input['status'], $assignment_id);
        if($crucial) {
            return response()->json(["success" => "Success added agreement"], StatusCode::OK);
        } else {
            return response()->json(["error" => "An error occured"], StatusCode::BAD_REQUEST);
        }
    }

    public function penalty($status, $assignment_id)
    {
        //^ assignment
        $assignment = Assignment::where("id", $assignment_id)->first();
        
        //^ borrowing
        $borrowing_id = $assignment['borrowing_id'];
        $borrowing = Borrowing::where("id", $borrowing_id)->first();
        
        //^ user
        $user_id = $borrowing['user_id'];
        $user = User::where("id", $user_id)->first();
        if($status == false) {
            $user = User::where('id', $user_id)
                            ->update([
                                'penalty' => $user['penalty'] + 1
                            ]);
        }
    }
}
