<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Exam;
use App\Http\Requests\QuestionRequest;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with('exam.subject');

        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $query->whereHas('exam', function ($examQuery) use ($subjectIds) {
                $examQuery->whereIn('subject_id', $subjectIds);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'LIKE', "%$search%")
                    ->orWhereHas('exam.subject', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $questions = $query->orderBy('id', 'desc')->paginate(10, ['*'], 'questions_page')->withQueryString();
        return view('admin.questions.questions', compact('questions'));
    }


    public function create()
    {
        $exams = Exam::all();

        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $exams = Exam::whereIn('subject_id', $subjectIds)->get();
        }
        return view('admin.questions.create_question', compact('exams'));
    }


    public function store(QuestionRequest $request)
    {
        $exam = Exam::findOrFail($request->exam_id);
        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $exams = Exam::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();
            if (!in_array($exam->id, $exams)) {
                return redirect()->back()
                    ->withInput()->with('warning', 'You cannot add a question to a subject you do not teach.');
            }
        }
        $currentTotal = Question::where('exam_id', $request->exam_id)
            ->sum('mark');

        if ($currentTotal + $request->mark > $exam->total_marks) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'The total marks of the questions cannot exceed the exam total marks.');
        }
        Question::create($request->validated());
        return redirect()->route('questions.index')->with('success', 'question created successfully.');
    }


    public function edit(Question $question)
    {
        $exams = Exam::all();
        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $exams = Exam::whereIn('subject_id', $subjectIds)->get();
            $examIds = $exams->pluck('id')->toArray();
            abort_if(!in_array($question->exam_id, $examIds), 404);
        }
        return view('admin.questions.edit_question', compact('question', 'exams'));
    }


    public function update(QuestionRequest $request, Question $question)
    {
        $exam = Exam::findOrFail($request->exam_id);
        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $exams = Exam::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();
            if (!in_array($exam->id, $exams)) {
                return redirect()->back()
                    ->withInput()->with('warning', 'You cannot edit a question to a subject you do not teach.');
            }
        }
        $currentTotal = Question::where('exam_id', $request->exam_id)
            ->where('id', '!=', $question->id)
            ->sum('mark');


        if ($currentTotal + $request->mark > $exam->total_marks) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'The total marks of the questions cannot exceed the exam total marks.');
        }
        $question->update($request->validated());
        return redirect()->route('questions.index')->with('success', 'question updated successfully.');
    }


    public function destroy(Question $question)
    {
        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $exams = Exam::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();
            if (!in_array($question->exam_id, $exams)) {
                return redirect()->back()
                    ->withInput()->with('warning', 'You cannot delete a question to a subject you do not teach.');
            }
        }
        $question->delete();
        return redirect()->route('questions.index')->with('success', 'question deleted successfully.');
    }
}
