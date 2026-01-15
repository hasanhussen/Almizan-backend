<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as AuthCanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail, AuthCanResetPassword
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'year',
        'subject_success',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'user_exam')->withPivot('code')->withPivot('is_submitted');
    }

    public function answers()
    {
        return $this->belongsToMany(Answer::class, 'user_answer');
    }

    public function subjectResults()
    {
        return $this->belongsToMany(Subject::class, 'subjectresults')->withPivot('total_degree');
    }

    public function subjectTeachers()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject');
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
