<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use App\Models\UserExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function getQuestions(Request $request)
    {
        $user  = Auth::user();
        $user = User::with('exams')->find($user->id);
        $exam = $user->exams()->where('exam_id', $request->exam_id)->first();
        // if (!$exam) {
        //     return response()->json(['message' => 'You are not authorized to access this exam.'], 403);
        // }
        $isSubmitted = UserExam::where('exam_id', $request->exam_id)->where('user_id', $user->id)->value('is_submitted');
        if ($isSubmitted) {
            return response()->json(['message' => 'you have already done this exam'], 403);
        }
        $questions = Question::where('exam_id', $request->exam_id)->with('answers')->get();
        $duration = Exam::where('id', $request->exam_id)->value('actual_duration') * 60;
        UserExam::where('exam_id', $request->exam_id)->where('user_id', $user->id)->update(['is_submitted' => '1']);
        return response()->json(['questions' => $questions, 'duration' => $duration]);
    }
}
