<?php

namespace App\Http\Controllers;

use App\Mail\PeminjamanEmail;
use App\Models\Assignment;
use App\Models\Borrowing;
use App\Models\Picture;
use App\Models\User;
use App\StatusCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:student']);
    }
    
    public function index()
    {
        $borrowing = Borrowing::all();
        return response()->json(["message" => "Success", "data" => $borrowing], 200);
    }

    
    public function historyBorrowing(User $user, $per_page = 5)
    {
        $getuser = Auth::user();
        $userId = $getuser['id'];
        $borrowings = $user->find($userId)->borrowings()->orderBy("id", "desc")->paginate($per_page);
        $count = count($borrowings);
        for ($i=0; $i < $count; $i++) {
            $borrowings[$i]['created'] = Carbon::parse($borrowings[$i]['created_at'])->diffForHumans();
        }
        return response()->json(["message" => "success", "data" => $borrowings], 200);
    }

    public function requestBorrowing(Request $request, User $user)
    {
        $getuser = Auth::user();
        $userId = $getuser['id'];
        $input = $request->all();
        $namaguru = $input["teacher_in_charge"];
        $guru = User::where('name', $namaguru)->get("email");

        //^ mengecek selisih tgl peminjaman & tgl request untuk mengetahui tipe URGENT/tidak
        $input['borrow_date'] = date_create($input['borrow_date']);
        $now = now()->timezone('Asia/Jakarta');
        $selisih = $this->selisih($input['borrow_date'], $now);

        if($selisih == true) {
            $validator = Validator::make($input, [
                "necessity" => "required",
                "teacher_in_charge" => "required",
                "borrow_date" => "required"
            ]);
            if($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            if($request->reason != null) {
                unset($input['reason']);
            }
            $input['urgent'] = false;
            $input['user_id'] = $userId;
            $borrowing = Borrowing::create($input);
            $borrowingId = $borrowing->id;
            if($borrowing) {
                $this->notifpinjam($guru, $borrowingId);
                return response()->json(['message' => "Succesfully sending Borrowing request", "data" => $input], 200);
            }
        }
        if($selisih == false) {
            $validator = Validator::make($input, [
                "necessity" => "required",
                "teacher_in_charge" => "required",
                "reason" => "required",
                "borrow_date" => "required"
            ]);
            if($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $input['urgent'] = true;
            $input['reason'] = $request->reason;
            $input['user_id'] = $userId;
            $borrowing = Borrowing::create($input);
            $borrowingId = $borrowing->id;
            if($borrowing) {
                $this->notifpinjam("ariq2901@gmail.com", $borrowingId);
                return response()->json(['message' => "Succesfully sending Borrowing request", "data" => $input], 200);
            }
        }

        return response()->json(["error" => "There is an error occured!"], 400);
    }

    public function validateBorrowing()
    {
        return Validator::make(request()->all(), [
            'necessity' => 'required',
            'teacher_in_charge' => 'required',
            'borrow_date' => 'required'
        ]);
    }

    public function selisih($tujuan, $wakturequest) {
        $h_tujuan = date_format($tujuan, "Y-m-d");
        $h_wakturequest = date_format($wakturequest, "Y-m-d");
        $interval = date_diff($wakturequest, $tujuan);
        $day = $interval->days;
        if($h_wakturequest < $h_tujuan) {
            return true;
        }
        if($h_wakturequest == $h_tujuan) {
            return false;
        }
    }

    public function notifpinjam($penerima, $borrowingId)
    {
        Mail::to($penerima, "Loan")->send(new PeminjamanEmail($borrowingId));

        return response()->json(["Email has been sent"], StatusCode::OK);
    }

    public function setorTugas(Request $request, $borrowingId)
    {

        $validator = Validator::make($request->all(), [
            "description" =>"required",
            "image" => "required|image:jpeg,png,gif,svg|max:2048"
            ]);
        if($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 500);
        }
        $input = $request->all();
        $input['borrowing_id'] = $borrowingId;
        $assignment = Assignment::create($input);
        
        // $uploadFolder = 'users';
        // $file = $request->file('image')->storeAs($uploadFolder, $finalName, 'public');
        // $input = $request->all();
        // $uploadedImageResponse = array(
            //     "image" => basename($file),
            //     "image_url" => Storage::disk('public')->url($file),
            //     "mime" => $request->file('image')->getClientMimeType()
            // );
            
        //^ Upload Images
        $md5 = md5_file($request->file('image')->getRealPath());
        $ext = $request->file('image')->guessExtension();
        $finalName = $md5 . '.' . $ext;
        $request->file('image')->storeAs("/images/assignments/$assignment->id/", $finalName, 'public');
        // Storage::put("public/images/assignments/$assignment->id/" . $finalName   , $request->file('image'));
        $assignmentId = $assignment->id;
        $pictureInput = [];
        $pictureInput['assignment_id'] = $assignmentId;
        $pictureInput['image'] = $finalName;
        Picture::create($pictureInput);

        if($assignment) {
            return response()->json(["message" => "Assignment successfully added"], 200);
        }
        return response()->json(["error" => "an error occured"], 400);
    }
}
