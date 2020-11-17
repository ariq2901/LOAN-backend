<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crucial extends Model
{
    use HasFactory;
    protected $table = "crucial";
    protected $fillable = ["assignment_id", "safe"];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}
