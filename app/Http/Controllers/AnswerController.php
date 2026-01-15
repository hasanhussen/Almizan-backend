<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Http\Requests\AnswerRequest;
use Illuminate\Http\Request;
use App\Models\Exam;

class AnswerController extends Controller
{
    public function index(Request $request)
    {
        $query = Answer::with('question');
        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $query->whereHas('question.exam', function ($examQuery) use ($subjectIds) {
                $examQuery->whereIn('subject_id', $subjectIds);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('answer_text', 'LIKE', "%$search%")
                    ->orWhereHas('question', function ($qq) use ($search) {
                        $qq->where('question_text', 'LIKE', "%{$search}%");
                    });
            });
        }

        $answers = $query->orderBy('id', 'desc')->paginate(10, ['*'], 'answers_page')->withQueryString();
        return view('admin.answers.answers', compact('answers'));
    }


    public function create()
    {
        $questions = Question::all();
        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $examIds = Exam::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();
            $questions = Question::whereIn('exam_id', $examIds)->get();
        }
        return view('admin.answers.create_answer', compact('questions'));
    }


    public function store(AnswerRequest $request)
    {

        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $examIds = Exam::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();
            $questionIds = Question::whereIn('exam_id', $examIds)->pluck('id')->toArray();
            if (!in_array($request->question_id, $questionIds)) {
                return redirect()->back()
                    ->withInput()->with('warning', 'You cannot add answers to this question.');
            }
        }
        foreach ($request->answers as $index => $answerText) {
            Answer::create([
                'question_id' => $request->question_id,
                'answer_text' => $answerText,
                'is_correct'  => isset($request->is_correct[$index]),
            ]);
        }

        // return redirect()->back()->with('success', 'Answers added successfully');

        return redirect()->route('answers.index')->with('success', 'Answers added successfully.');
    }


    public function edit(Answer $answer)
    {
        $question = $answer->question;


        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $examIds = Exam::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();
            $questions = Question::whereIn('exam_id', $examIds)->get();
            $questionIds = $questions->pluck('id')->toArray();
            abort_if(!in_array($answer->question_id, $questionIds), 404);
        }

        $answers = $question->answers;

        return view('admin.answers.edit_answer', compact('question', 'answers'));
    }


    public function update(Request $request, Question $question)
    {
        // Validation
        $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*' => 'required|string',
            'is_correct' => 'required|array|min:1', // على الأقل جواب صحيح واحد
        ]);

        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $examIds = Exam::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();
            $questionIds = Question::whereIn('exam_id', $examIds)->pluck('id')->toArray();
            if (!in_array($question->id, $questionIds)) {
                return redirect()->back()
                    ->withInput()->with('warning', 'You cannot edit a question to a subject you do not teach.');
            }
        }

        // تحديث كل جواب
        foreach ($request->answers as $answerId => $answerText) {
            Answer::where('id', $answerId)->update([
                'answer_text' => $answerText,
                'is_correct'  => in_array($answerId, $request->is_correct),
            ]);
        }

        // Redirect مع رسالة نجاح
        return redirect()->route('answers.index')
            ->with('success', 'Answers updated successfully');
    }


    public function destroy(Answer $answer)
    {
        $question = $answer->question;
        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $exams = Exam::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();
            if (!in_array($question->exam_id, $exams)) {
                return redirect()->back()
                    ->withInput()->with('warning', 'You cannot delete a answer to a qeustion you do not teach.');
            }
        }

        $correctCount = $question->answers()->where('is_correct', true)
            ->where('id', '!=', $answer->id)
            ->count();

        if ($correctCount == 0) {
            $question->answers()->delete();
            $question->delete();

            return redirect()->route('answers.index')->with('success', 'Answer and its question deleted because it was the last correct answer.');
        } else {
            $answer->delete();
            return redirect()->route('answers.index')->with('success', 'Answer deleted successfully.');
        }
    }


    public function checkDelete(Answer $answer)
    {
        $question = $answer->question;
        $user = auth()->user();

        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $examIds = Exam::whereIn('subject_id', $subjectIds)->pluck('id')->toArray();

            abort_if(!in_array($question->exam_id, $examIds), 403);
        }

        $correctCount = $question->answers()
            ->where('is_correct', true)
            ->where('id', '!=', $answer->id)
            ->count();

        return response()->json([
            'willDeleteQuestion' => $correctCount == 0
        ]);
    }
}
