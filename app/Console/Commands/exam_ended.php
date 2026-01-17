<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Exam;
use App\Models\Result;
use App\Models\UserExam;
use App\Services\NotificationService;

class exam_ended extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:exam_ended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users that the exam has ended';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Damascus');
        $notificationService = new NotificationService();

        // جلب الامتحانات التي انتهت اليوم والوقت الحالي >= end_time
        $exams = Exam::where('exam_date', $now->toDateString())
            ->where('exam_status', '1') // قيد التشغيل
            ->get()
            ->filter(function ($exam) use ($now) {
                if (!$exam->end_time) return false;
                $endDateTime = Carbon::parse($exam->exam_date . ' ' . $exam->end_time, 'Asia/Damascus');
                return $now->gte($endDateTime);
            });

        foreach ($exams as $exam) {


            // إرسال الإشعارات
            foreach ($exam->users as $user) {
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
            }
            $exam->update(['exam_status' => '2']);
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
    }
}
