<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $table = "assignment";
    protected $fillable = ["borrowing_id", "description"];

    public function picture()
    {
        return $this->hasMany(Picture::class);
    }

    public function crucial()
    {
        return $this->hasOne(Assignment::class);
    }
}
