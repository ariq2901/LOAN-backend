<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;
    protected $table = "borrowing";
    protected $fillable = ["urgent", "necessity", "teacher_in_charge", "user_id", "reason", "borrow_date", "approved", "teacher_reason"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignment()
    {
        return $this->hasOne(Assignment::class);
    }
}
