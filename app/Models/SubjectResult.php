<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class SubjectResult extends Model
{
    use HasFactory;
    protected $table = 'subjectresults';
    protected $fillable = [
        'user_id',
        'subject_id',
        'total_degree',
        'is_passed',
    ];
}
