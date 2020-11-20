<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avtar extends Model
{
    use HasFactory;
    protected $table="avatar";
    protected $fillable= ["user_id", "image"];

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
