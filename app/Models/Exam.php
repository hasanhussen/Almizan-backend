<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Exam extends Model
{
    use HasFactory;
    protected $fillable = [
        'subject_id',
        'exam_date',
        'total_marks',
        'success_rate',
        'exam_term',
        'exam_type',
        'year',
        'start_time',
        'end_time',
        'actual_duration',
        'exam_status'
    ];

    // protected $appends = ['is_open'];

    // public function getIsOpenAttribute()
    // {
    //     $now = \Carbon\Carbon::now('Asia/Damascus');
    //     $start  = \Carbon\Carbon::createFromFormat('H:i:s', $this->start_time, 'Asia/Damascus');
    //     $end = \Carbon\Carbon::createFromFormat('H:i:s', $this->end_time, 'Asia/Damascus');
    //     return $now->between($start, $end);
    // }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_exam')->withPivot('code');
    }
}
