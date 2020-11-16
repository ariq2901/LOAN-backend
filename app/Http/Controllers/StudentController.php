<?php

namespace App\Http\Controllers;

use App\Mail\PeminjamanEmail;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

    
    public function historyBorrowing(User $user)
    {
        $getuser = Auth::user();
        $userId = $getuser['id'];
        $borrowings = $user->find($userId)->borrowings;
        if(count($borrowings) > 0) {
            return response()->json(["message" => "success", "data" => $borrowings], 200);
        }
        return response()->json(["message" => "user hasn't had a borrowing request"], 200);
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
        Mail::to($penerima, "Tim sukses")->send(new PeminjamanEmail($borrowingId));

        return response()->json(["Email has been sent"], 200);
    }
}
