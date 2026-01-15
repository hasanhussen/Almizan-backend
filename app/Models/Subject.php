<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Subject extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'year',
        'success_rate',
        'semester',
        'mark'
    ];

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'specialty_subject');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'subjectresults');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_subject');
    }
}
