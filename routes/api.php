<?php

use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordCodeController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\AppInfoApiController;
use App\Http\Controllers\Api\PolicyApiController;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);

    Route::post('/forgot-password', [PasswordCodeController::class, 'sendCode']);
    Route::post('/verify-code', [PasswordCodeController::class, 'verifyCode']);
    Route::post('/reset-password', [NewPasswordController::class, 'resetPassword']);

    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware(['signed'])
        ->name('verification.verify');


    Route::middleware(['auth:sanctum'])->post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent!']);
    })->name('verification.send');
});


Route::middleware(['auth:sanctum', 'throttle:60,1', 'verified'])->group(function () {
    Route::get('/getProfile', [UserController::class, 'getProfile']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/getSlider', [SliderController::class, 'getSlider']);
    Route::get('/subjects', [SubjectController::class, 'getSubjects']);
    Route::post('/checkCode', [ExamController::class, 'checkCode']);
    Route::post('/questions', [QuestionController::class, 'getQuestions']);
    Route::post('/submitExam', [AnswerController::class, 'submitExam']);
    Route::get('/appInfo', [AppInfoApiController::class, 'index']);
    Route::get('/policy', [PolicyApiController::class, 'index']);
});
