<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AppInfoController;
use App\Http\Controllers\PolicyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::middleware(['throttle:60,1'])->group(function () {

    Route::get('/', function () {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('admin.auth.login');
    })->middleware('guest')->name('admin-login');


    Route::post('/login', [UserController::class, 'login'])->name('login');
    Route::get('/logout', function () {
        Auth::logout();
        return redirect()->route('admin-login');
    })->name('logout');

    Route::get('/welcome', function () {
        return view('welcome');
    });

    Route::prefix('admin')->middleware('auth')->group(function () {

        Route::get('/dash', function () {
            return redirect()->route('users.index');
        })->name('home');


        // Users
        Route::prefix('users')->name('users.')->group(function () {

            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy')->middleware('role:admin');
            Route::put('/{id}/update-role', [UserController::class, 'updateUserRole'])->name('updateRole')->middleware('role:admin');
            Route::post('/promote', [UserController::class, 'promote'])
                ->name('promote')->middleware('role:admin');

            Route::middleware('role:admin|supervisor')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('/create', [UserController::class, 'create'])->name('create');
                Route::post('/store', [UserController::class, 'store'])->name('store');
                Route::get('/{user}/show', [UserController::class, 'show'])->name('show');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                Route::patch('/{user}', [UserController::class, 'updateProfile'])->name('update');
                Route::patch('/{id}/ban', [UserController::class, 'ban'])->name('ban');
            });
        });


        Route::prefix('specialties')->name('specialties.')->middleware('role:admin|supervisor')->group(function () {
            Route::get('/', [SpecialtyController::class, 'index'])->name('index');
            Route::get('/create', [SpecialtyController::class, 'create'])->name('create');
            Route::post('/', [SpecialtyController::class, 'store'])->name('store');
            Route::get('/{specialty}/edit', [SpecialtyController::class, 'edit'])->name('edit');
            Route::patch('/{specialty}', [SpecialtyController::class, 'update'])->name('update');
            Route::delete('/{specialty}', [SpecialtyController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('sliders')->name('sliders.')->middleware('role:admin|supervisor')->group(function () {
            Route::get('/', [SliderController::class, 'index'])->name('index');
            Route::get('/create', [SliderController::class, 'create'])->name('create');
            Route::post('/', [SliderController::class, 'store'])->name('store');
            Route::get('/{slider}/edit', [SliderController::class, 'edit'])->name('edit');
            Route::patch('/{slider}', [SliderController::class, 'update'])->name('update');
            Route::delete('/{slider}', [SliderController::class, 'destroy'])->name('destroy');
        });


        Route::get('/exams', [ExamController::class, 'index'])->middleware('role:admin|supervisor|teacher')->name('exams.index');
        Route::prefix('exams')->name('exams.')->middleware('role:admin|supervisor')->group(function () {

            Route::get('/create', [ExamController::class, 'create'])->name('create');
            Route::post('/', [ExamController::class, 'store'])->name('store');
            Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
            Route::patch('/{exam}', [ExamController::class, 'update'])->name('update');
            Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
            Route::post('/{exam}/end', [ExamController::class, 'endExam'])->name('endExam');
            Route::post('/{exam}/start', [ExamController::class, 'startExam'])->name('startExam');
            Route::post('/{exam}/update-date', [ExamController::class, 'updateDate'])->name('updateDate');
        });

        Route::prefix('students')->name('students.')->middleware('role:admin|supervisor')->group(function () {

            Route::get('/{exam}/create', [StudentController::class, 'create'])->name('create');
            Route::post('/{exam}/store', [StudentController::class, 'store'])->name('store');
            Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit');
            Route::patch('/{student}', [StudentController::class, 'update'])->name('update');
            Route::delete('/{student}/{exam}', [StudentController::class, 'destroy'])->name('destroy');
            Route::post('/{student}/end', [StudentController::class, 'endExam'])->name('endExam');
            Route::post('/{student}/banExam', [StudentController::class, 'banExam'])->name('banExam');
            Route::post('/{student}/resetExam', [StudentController::class, 'resetExam'])->name('resetExam');
            Route::get('/{exam}/students', [StudentController::class, 'students'])->name('index');
            Route::post('/{exam}/generate-codes', [StudentController::class, 'generateCodes'])->name('generateCodes');
        });


        Route::prefix('subjects')->name('subjects.')->group(function () {
            Route::get('/', [SubjectController::class, 'index'])->name('index')->middleware('role:admin|supervisor|teacher');
            Route::get('/create', [SubjectController::class, 'create'])->name('create')->middleware('role:admin|supervisor');
            Route::post('/', [SubjectController::class, 'store'])->name('store')->middleware('role:admin|supervisor');
            Route::get('/{subject}/edit', [SubjectController::class, 'edit'])->name('edit')->middleware('role:admin|supervisor');
            Route::patch('/{subject}', [SubjectController::class, 'update'])->name('update')->middleware('role:admin|supervisor');
            Route::delete('/{subject}', [SubjectController::class, 'destroy'])->name('destroy')->middleware('role:admin|supervisor');
            Route::get('/by-year/{year}', [SubjectController::class, 'byYear']);
        });

        Route::prefix('questions')->name('questions.')->middleware('role:admin|supervisor|teacher')->group(function () {
            Route::get('/', [QuestionController::class, 'index'])->name('index');
            Route::get('/create', [QuestionController::class, 'create'])->name('create');
            Route::post('/', [QuestionController::class, 'store'])->name('store');
            Route::get('/{question}/edit', [QuestionController::class, 'edit'])->name('edit');
            Route::patch('/{question}', [QuestionController::class, 'update'])->name('update');
            Route::delete('/{question}', [QuestionController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('answers')->name('answers.')->middleware('role:admin|supervisor|teacher')->group(function () {
            Route::get('/', [AnswerController::class, 'index'])->name('index');
            Route::get('/create', [AnswerController::class, 'create'])->name('create');
            Route::post('/', [AnswerController::class, 'store'])->name('store');
            Route::get('/{answer}/edit', [AnswerController::class, 'edit'])->name('edit');
            Route::patch('/{question}', [AnswerController::class, 'update'])->name('update');
            Route::delete('/{answer}', [AnswerController::class, 'destroy'])->name('destroy');
            Route::get('/{answer}/check-delete', [AnswerController::class, 'checkDelete'])
                ->name('checkDelete');
        });

        Route::prefix('results')->name('results.')->middleware('role:admin|supervisor|teacher')->group(function () {

            Route::get('/', [ResultController::class, 'index'])->name('index');
            Route::get('/exam/{examId}', [ResultController::class, 'examResults'])->name('exam');
            Route::get('/answers/{userId}/{examId}', [ResultController::class, 'viewAnswers'])->name('answers');
            Route::patch('/{result}/update-mark', [ResultController::class, 'updateMark'])
                ->name('updateMark');
        });


        Route::middleware(['auth', 'role:admin'])->group(function () {
            Route::get('app-info', [AppInfoController::class, 'index'])->name('admin.app-info.index');
            Route::post('app-info', [AppInfoController::class, 'store'])->name('admin.app-info.store');
        });

        Route::middleware(['auth', 'role:admin'])->group(function () {
            Route::get('policy', [PolicyController::class, 'index'])->name('admin.policy.index');
            Route::post('policy', [PolicyController::class, 'store'])->name('admin.policy.store');
        });
    });
});
