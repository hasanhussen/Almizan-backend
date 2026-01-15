<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Exam;
use App\Models\UserAnswer;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Result;
use App\Models\UserExam;
use App\Models\Subject;
use App\Models\SubjectResult;


class AnswerController extends Controller
{
    public function submitExam(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_id' => 'required|exists:answers,id',
        ]);

        $userId = Auth::id();

        $exam = Exam::findOrFail($request->exam_id);
        $subjectId = $exam->subject_id;

        $correctCount = 0;
        $wrongCount = 0;
        $totalCorrectMarks = 0;

        DB::transaction(function () use (
            $request,
            $userId,
            &$correctCount,
            &$wrongCount,
            &$totalCorrectMarks
        ) {
            foreach ($request->answers as $item) {

                // تخزين إجابة الطالب
                UserAnswer::create([
                    'user_id' => $userId,
                    'question_id' => $item['question_id'],
                    'answer_id' => $item['answer_id'],
                    'exam_id' => $request->exam_id
                ]);

                $answer = Answer::where('id', $item['answer_id'])
                    ->where('question_id', $item['question_id'])
                    ->first();

                if ($answer && $answer->is_correct) {
                    $question = Question::find($item['question_id']);
                    $totalCorrectMarks += $question->mark;
                    $correctCount++;
                } else {
                    $wrongCount++;
                }
            }
        });

        $totalCorrectMarks = round($totalCorrectMarks);

        if ($totalCorrectMarks >= $exam->success_rate) {
            $isPassed = true;
        } else {
            $isPassed = false;
        }

        Result::updateOrCreate(
            [
                'user_id' => $userId,
                'subject_id' => $subjectId,
                'exam_id' => $exam->id,
            ],
            [
                'exam_degree' => $totalCorrectMarks,
                'is_passed' => $isPassed,
            ]
        );

        UserExam::updateOrCreate(
            [
                'user_id' => $userId,
                'exam_id' => $exam->id,
            ],
            'is_submitted',
            '2'
        );

        // إذا كان الامتحان Final، نجمع كل درجات الطالب في المادة
        if ($exam->exam_type === 'final') {
            $examsInTerm = Exam::where('subject_id', $subjectId)
                ->where('exam_term', $exam->exam_term)
                ->get();

            $totalDegree = 0;

            foreach ($examsInTerm as $examItem) {
                $result = Result::where('user_id', $userId)
                    ->where('exam_id', $examItem->id)
                    ->first();

                if ($result) {

                    $totalDegree += $result->exam_degree;
                } else {

                    if ($examItem->exam_type === 'final') {
                        $totalDegree += 0;
                    } else {
                        $lastResult = Result::where('user_id', $userId)
                            ->where('subject_id', $subjectId)
                            ->whereHas('exam', function ($q) use ($examItem) {
                                $q->where('exam_type', $examItem->exam_type);
                            })
                            ->orderBy('created_at', 'desc')
                            ->first();
                        if ($lastResult) {
                            $totalDegree += $lastResult->exam_degree;
                        }
                    }
                }
            }

            $subject = Subject::findOrFail($subjectId);

            $finalPassed = $totalDegree >= $subject->success_rate;

            SubjectResult::updateOrCreate(
                [
                    'user_id' => $userId,
                    'subject_id' => $subjectId,
                ],
                [
                    'total_degree' => $totalDegree,
                    'is_passed' => $finalPassed,
                ]
            );

            if ($finalPassed) {
                $user = Auth::user();
                $user->subject_success += 1;
                $user->save();
            }

            //subject_success
        }


        return response()->json([
            'message' => 'تم تسليم الامتحان بنجاح',
            'data' => [
                'correct_answers_count' => $correctCount,
                'wrong_answers_count' => $wrongCount,
                'total_marks' => $totalCorrectMarks,
                'success_rate' => $exam->success_rate,
                'is_passed' => $isPassed,
            ]
        ]);
    }
}
