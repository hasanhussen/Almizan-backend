<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentsRequest;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\User;
use Carbon\Carbon;
use App\Models\UserExam;
use App\Models\Result;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function students(Exam $exam)
    {
        $students = UserExam::with('user')->where('exam_id', $exam->id)->orderBy('id')->paginate(15);
        return view('admin.exams.students.students', compact('exam', 'students'));
    }


    public function generateCodes(Exam $exam)
    {

        UserExam::where('exam_id', $exam->id)
            ->whereNull('code')
            ->chunkById(100, function ($students) {
                foreach ($students as $item) {
                    $item->update([
                        'code' => strtoupper(Str::random(6))
                    ]);
                }
            });

        return back()->with('success', 'Codes generated successfully.');
    }

    public function banExam(UserExam  $student)
    {
        $exam = Exam::findOrFail($student->exam_id);
        Result::updateOrCreate(
            [
                'user_id' => $student->user_id,
                'subject_id' => $exam->subject_id,
                'exam_id' => $student->exam_id,
            ],
            [
                'exam_degree' => 0,
                'is_passed' => false,

            ]
        );
        $student->update(['is_submitted' => '1', 'revoked_status' => '2']);
        return back()->with('success', 'Student baned successfully.');
    }

    public function resetExam(UserExam $student)
    {
        $student->update(['is_submitted' => '0']);

        return back()->with('success', 'Student can retake the exam.');
    }

    public function endExam(UserExam $student)
    {
        $now = Carbon::now('UTC');
        $notificationService = new \App\Services\NotificationService();
        $user = User::findOrFail($student->user_id);
        $exam = Exam::findOrFail($student->exam_id);
        $notificationService->sendToUser(
            $user,
            'Exam Ended',
            "The exam for subject {$exam->subject->name} has ended.",
            [
                'type' => 'exam_ended',
                'exam_id' => $exam->id,
                'subject_name' => $exam->subject->name,
                'end_time' => $exam->end_time,
            ]
        );

        $student->update(['is_submitted' => '2']);
        return back()->with('success', 'Exam for this student ended successfully.');
    }


    public function create(Exam $exam)
    {
        $students = User::role(['student'])->get();
        return view('admin.exams.students.create_student', compact('students', 'exam'));
    }

    public function store(StudentsRequest $request, Exam $exam)
    {
        UserExam::create([
            'user_id' => $request->user_id,
            'exam_id' => $request->exam_id,
        ]);

        return redirect()
            ->route('students.index', $exam->id)
            ->with('success', 'Student added successfully.');
    }

    public function destroy(UserExam $student, Exam $exam)
    {
        if ($exam->exam_status != '0') {
            return redirect()->route('students.index', $exam->id)->with('error', 'The student cannot be deleted..');
        }
        $student->delete();
        return redirect()->route('students.index', $exam->id)->with('success', 'Student deleted successfully.');
    }
}
