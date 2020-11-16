<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            if(Borrowing::create($input)) {
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
            if(Borrowing::create($input)) {
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

    public function getDate()
    {
        $user = Auth::user();
        $created_at = $user['created_at'];
        $created_at = date_format($created_at, "Y/m/d H:i:s");
        $nowTime = date('Y/m/d H:i:s');
        //^ inisialisasi waktu
        $create = $user['created_at'];
        $custom = date_create('2020-11-14');
        $ini = now()->timezone('Asia/Jakarta');
        //^ ubah ke format int date
        $pembuatan = strtotime($created_at);
        $waktuIni = strtotime($nowTime);
        //^ selisih waktu /jam
        $interval = date_diff($create, $ini);
    
        return response()->json(["now time" => $waktuIni, "user created_at" => $pembuatan, "selisih waktu" => $interval], 200);
    }

    public function selisih($tujuan, $wakturequest) {
        $h_tujuan = date_format($tujuan, "Y-m-d");
        $h_wakturequest = date_format($wakturequest, "Y-m-d");
        $hari = [$h_tujuan, $h_wakturequest];
        $interval = date_diff($wakturequest, $tujuan);
        $day = $interval->days;
        $guru = "masuk ke guru";
        $ustad = "masuk ke ustad";
        if($h_wakturequest < $h_tujuan) {
            return true;
        }
        if($h_wakturequest == $h_tujuan) {
            return false;
        }
    }
}
