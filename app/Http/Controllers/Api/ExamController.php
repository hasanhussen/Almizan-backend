<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserExam;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function checkCode(Request $request)
    {
        $user  = Auth::user();
        $exam = $user->exams()->where('exam_id', $request->exam_id)->first();
        $isSubmitted = UserExam::where('exam_id', $request->exam_id)->where('user_id', $user->id)->value('is_submitted');
        if ($isSubmitted != '0') {
            return response()->json([
                'valid' => false,
                'message' => 'you have already done this exam'
            ]);
        }
        if ($exam && $exam->pivot->code === $request->code) {
            return response()->json(['valid' => true]);
        } else {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid code'
            ]);
        }
    }
}
