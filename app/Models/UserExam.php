<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class UserExam extends Model
{
    use HasFactory;
    protected $table = 'user_exam';

    protected $fillable = [
        'user_id',
        'exam_id',
        'code',
        'is_submitted',
        'revoked_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
