<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Specialty extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'student_count',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'specialty_subject');
    }
}
