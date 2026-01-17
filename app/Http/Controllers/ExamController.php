<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\UserExam;
use App\Models\User;
use App\Models\SubjectResult;
use Carbon\Carbon;
use App\Http\Requests\ExamRequest;
use Illuminate\Http\Request;
use App\Models\Result;
use Illuminate\Support\Str;

class ExamController extends Controller
{


    public function startExam(Exam $exam)
    {
        $now = Carbon::now('Asia/Damascus');

        // 1️⃣ حالة الامتحان
        if ($exam->exam_status != '0') {
            return back()->with(
                'warning',
                'Cannot start exam manually. The exam has already started or ended.'
            );
        }

        // 2️⃣ التاريخ
        if ($exam->exam_date !== $now->toDateString()) {
            return back()->with(
                'warning',
                'Cannot start exam manually. The exam is not scheduled for today.'
            );
        }

        // 3️⃣ وقت النهاية (لازم ما يكون خلص)
        if ($exam->end_time) {
            $endTime = Carbon::parse(
                $exam->exam_date . ' ' . $exam->end_time,
                'Asia/Damascus'
            );

            if ($now->gte($endTime)) {
                return back()->with(
                    'warning',
                    'Cannot start exam manually. The exam time has already ended.'
                );
            }
        }

        // ✅ بدء يدوي (تغيير وقت البداية)
        $exam->update([
            'start_time'  => $now->format('H:i:s'),
            'exam_status' => '1',
        ]);

        // إشعارات
        $users = $exam->users;

        $notificationService = new \App\Services\NotificationService();

        foreach ($users as $user) {
            $notificationService->sendToUser(
                $user,
                'Exam Started',
                "The exam for subject {$exam->subject->name} has started.",
                [
                    'type' => 'exam_started',
                    'exam_id' => $exam->id,
                    'subject_name' => $exam->subject->name,
                    'start_time' => $exam->start_time,
                    'end_time' => $exam->end_time,
                ]
            );
        }

        return back()->with('success', 'Exam started manually successfully.');
    }



    public function endExam(Exam $exam)
    {
        $now = Carbon::now('Asia/Damascus');

        // 1️⃣ لازم يكون الامتحان شغّال
        if ($exam->exam_status != '1') {
            return back()->with(
                'warning',
                'Cannot end exam. It has not started yet or already ended.'
            );
        }

        // 2️⃣ لازم يكون اليوم نفسه
        if ($exam->exam_date !== $now->toDateString()) {
            return back()->with(
                'warning',
                'Cannot end exam. The exam is not scheduled for today.'
            );
        }

        // 3️⃣ وقت البدء (تاريخ + وقت)
        if ($exam->start_time) {
            $startTime = Carbon::parse(
                $exam->exam_date . ' ' . $exam->start_time,
                'Asia/Damascus'
            );

            if ($now->lt($startTime)) {
                return back()->with(
                    'warning',
                    'Cannot end exam. The exam has not started yet.'
                );
            }
        }





        // ✅ إنهاء يدوي
        $exam->update([
            'end_time'    => $now->format('H:i:s'),
            'exam_status' => '2',
        ]);

        // إشعارات
        $users = $exam->users;

        $notificationService = new \App\Services\NotificationService();

        foreach ($users as $user) {
            $notificationService->sendToUser(
                $user,
                'Exam Ended',
                "The exam for subject {$exam->subject->name} has ended.",
                [
                    'type' => 'exam_ended',
                    'exam_id' => $exam->id,
                    'subject_name' => $exam->subject->name,
                    'start_time' => $exam->start_time,
                    'end_time' => $exam->end_time,
                ]
            );
        }

        $userExams = UserExam::where('exam_id', $exam->id)->where('is_submitted', '1')->get();

        foreach ($userExams as $userExam) {
            $userExam->update([
                'is_submitted'  => '2',
            ]);

                $result = Result::where([
        'user_id' => $userExam->user_id,
        'subject_id' => $exam->subject_id,
        'exam_id' => $exam->id,
    ])->first();

    // إذا ما عنده نتيجة، بس ساعتها ننشئها
    if (!$result) {
        Result::create([
            'user_id' => $userExam->user_id,
            'subject_id' => $exam->subject_id,
            'exam_id' => $exam->id,
            'exam_degree' => 0,
            'is_passed' => false,
        ]);
        }

        return back()->with('success', 'Exam ended successfully.');
    }

    public function index(Request $request)
    {
        $query = Exam::with('subject');
        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            $subjectIds = $user->subjectTeachers()->pluck('id')->toArray();
            $query->whereIn('subject_id', $subjectIds);
        }



        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->WhereHas('subject', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%");
            })->orWhere('exam_term', $search);
        }
        if ($request->filled('years')) {
            $query->whereIn('year', $request->years);
        }

        $exams = $query->orderBy('id', 'desc')->paginate(10, ['*'], 'exams_page')->withQueryString();


        return view('admin.exams.exams', compact('exams'));
    }


    public function create()
    {
        $subjects = Subject::all();
        return view('admin.exams.create_exam', compact('subjects'));
    }


    public function store(ExamRequest $request)
    {
        $examTerm = $request->term_name . ' ' . $request->season;

        $data = $request->validated();
        unset($data['term_name'], $data['season']);
        $data['exam_term'] = $examTerm;

        $subject = Subject::findOrFail($request->subject_id);

        $currentTotal = Exam::where('subject_id', $request->subject_id)
            ->where('exam_term', $examTerm)
            ->sum('total_marks');

        if ($currentTotal + $request->total_marks > $subject->mark) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Total marks of all exams for this subject and semester cannot exceed subject mark (' . $subject->mark . ').');
        }

        $exam = Exam::create($data);
        $this->generateEligibleStudents($exam);
        return redirect()->route('exams.index')->with('success', 'exam created successfully.');
    }


    public function edit(Exam $exam)
    {
        $subjects = Subject::all();

        $termName = null;
        $season   = null;

        if ($exam->exam_term) {
            $parts = explode(' ', $exam->exam_term);
            $season = array_pop($parts);          // 2025-2026
            $termName = implode(' ', $parts);     // First Semester
        }

        return view('admin.exams.edit_exam', compact('exam', 'subjects', 'termName', 'season'));
    }


    public function update(ExamRequest $request, Exam $exam)
    {


        $subject = Subject::findOrFail($request->subject_id);

        $examTerm = $request->term_name . ' ' . $request->season;

        $data = $request->validated();
        unset($data['term_name'], $data['season']);
        $data['exam_term'] = $examTerm;

        // مجموع علامات باقي الامتحانات لنفس المادة ونفس السنة/الفصل
        $currentTotal = Exam::where('subject_id', $request->subject_id)
            ->where('exam_term', $examTerm)
            ->where('id', '!=', $exam->id)
            ->sum('total_marks');

        if ($currentTotal + $request->total_marks > $subject->mark) {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Total marks of all exams for this subject and semester cannot exceed subject mark (' . $subject->mark . ').');
        }

        $exam->update($data);
        return redirect()->route('exams.index')->with('success', 'exam updated successfully.');
    }


    public function destroy(Exam $exam)
    {
        if ($exam->exam_status != '0') {
            return redirect()->route('exams.index')->with('error', 'The exam cannot be deleted..');
        }
        $exam->delete();
        return redirect()->route('exams.index')->with('success', 'exam deleted successfully.');
    }




    public function updateDate(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'start_time' => 'nullable|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i|after:start_time',
        ]);

        // $now = Carbon::now('Asia/Damascus');
        // $startDateTime = Carbon::parse($exam->exam_date . ' ' . $request->start_time, 'Asia/Damascus');
        // if($startDateTime->gte($now) && $exam->exam_status != '0') {
        //     $data['exam_status'] = '0';
        // }

        $exam->update($data);

        $this->generateEligibleStudents($exam);

        return redirect()
            ->route('exams.index')
            ->with('success', 'Exam dates updated.');
    }




    public function generateEligibleStudents(Exam $exam)
    {
        // حذف أي بيانات سابقة لهذا الامتحان
        UserExam::where('exam_id', $exam->id)->delete();

        // 1️⃣ طلاب السنة نفسها الذين لم ينجحوا في المادة
        $yearStudentsIds = User::where('year', $exam->year)
            ->whereDoesntHave('subjectResults', function ($q) use ($exam) {
                $q->where('subject_id', $exam->subject_id)
                    ->where('is_passed', true);
            })
            ->pluck('id');

        // 2️⃣ الطلاب الراسبين بالمادة (من أي سنة)
        $failedStudentsIds = SubjectResult::where('subject_id', $exam->subject_id)
            ->where('is_passed', false)
            ->pluck('user_id');

        // 3️⃣ دمج القائمتين بدون تكرار
        $eligibleStudentIds = $yearStudentsIds
            ->merge($failedStudentsIds)
            ->unique();

        // 4️⃣ الإضافة لجدول user_exam
        foreach ($eligibleStudentIds as $studentId) {
            UserExam::create([
                'user_id' => $studentId,
                'exam_id' => $exam->id,
            ]);
        }

        return back()->with('success', 'Eligible students generated successfully.');
    }
}
