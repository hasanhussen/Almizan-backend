<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Exam;
use App\Models\User;
use App\Models\UserAnswer;

use Illuminate\Http\Request;

class ResultController extends Controller
{


    public function index(Request $request)
    {
        $query = Result::with(['user', 'subject', 'exam']);
        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $query->whereIn('subject_id', $subjectIds);
        }
        // Dropdown exam terms
        $examTerms = Exam::query()
            ->select('exam_term')
            ->distinct()
            ->orderBy('exam_term')
            ->pluck('exam_term');

        $examTypes = [
            'quiz',
            'midterm',
            'final',
            'assignment',
            'project',
            'participation',
            'oral',
            'practice',
            'makeup'
        ];

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('subject', function ($sq) use ($search) {
                        $sq->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('exam', function ($eq) use ($search) {
                        $eq->where('exam_term', 'LIKE', "%{$search}%")
                            ->orWhere('exam_type', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filter by exam term
        if ($request->filled('exam_term')) {
            $query->whereHas('exam', function ($q) use ($request) {
                $q->where('exam_term', $request->exam_term);
            });
        }

        // Filter by exam type
        if ($request->filled('exam_type')) {
            $query->whereHas('exam', function ($q) use ($request) {
                $q->where('exam_type', $request->exam_type);
            });
        }

        // 🔥 الترتيب حسب الامتحان (الأحدث → الأقدم)
        $query->join('exams', 'results.exam_id', '=', 'exams.id')
            ->orderByDesc('exams.id')   // أو exams.created_at
            ->orderBy('results.id')
            ->select('results.*');

        $results = $query
            ->paginate(10, ['*'], 'results_page')
            ->withQueryString();

        return view('admin.results.results', compact('results', 'examTerms', 'examTypes'));
    }


    public function examResults(Request $request, $examId)
    {

        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $examIds = Exam::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();

            abort_if(!in_array($examId, $examIds), 403);
        }

        $query = Result::with(['user', 'subject', 'exam'])->where('exam_id', $examId);



        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('subject', function ($sq) use ($search) {
                        $sq->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('exam', function ($eq) use ($search) {
                        $eq->where('exam_term', 'LIKE', "%{$search}%")
                            ->orWhere('exam_type', 'LIKE', "%{$search}%");
                    });
            });
        }

        $results = $query
            ->paginate(10, ['*'], 'exam_results_page')
            ->withQueryString();
        $exam = Exam::findOrFail($examId);

        return view('admin.results.exam_results', compact('results', 'exam'));
    }


    public function viewAnswers($userId, $examId)
    {
        $userauth = auth()->user();
        if ($userauth->hasRole('teacher')) {
            $subjectIds = $userauth->subjectTeachers()->pluck('id')->toArray();
            $examIds = Exam::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();
            abort_if(!in_array($examId, $examIds), 403);
        }
        $user = User::findOrFail($userId);
        $Answers = UserAnswer::with([
            'question',
            'answer'
        ])->where('user_id', $userId)->where('exam_id', $examId);
        $userAnswers = $Answers
            ->paginate(10, ['*'], 'user_answers_page')
            ->withQueryString();
        return view('admin.results.view_answers', compact('userAnswers', 'user'));
    }

    public function updateMark(Request $request, Result $result)
    {
        $user = auth()->user();

        // صلاحيات
        abort_unless($user->hasRole(['admin', 'teacher']), 403);

        // تحقق المدرس من المادة
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            abort_if(!in_array($result->subject_id, $subjectIds), 403);
        }

        // validation
        $request->validate([
            'exam_degree' => [
                'required',
                'numeric',
                'min:0',
                'max:' . $result->exam->total_marks,
            ],
        ]);

        // تحديث النتيجة
        $result->update([
            'exam_degree' => $request->exam_degree,
            'is_passed'   => $request->exam_degree >= $result->exam->success_rate,
        ]);

        return back()->with('success', 'Result updated successfully.');
    }
}
