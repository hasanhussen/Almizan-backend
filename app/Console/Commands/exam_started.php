<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Exam;
use App\Services\NotificationService;

class exam_started extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:exam_started';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users that the exam has started';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Damascus');
        $notificationService = new NotificationService();

        $exams = Exam::where('exam_date', $now->toDateString())
            ->where('exam_status', '0') // قيد التشغيل
            ->get()
            ->filter(function ($exam) use ($now) {
                if (!$exam->start_time) return false;
                $startDateTime = Carbon::parse($exam->exam_date . ' ' . $exam->start_time, 'Asia/Damascus');
                return $now->gte($startDateTime);
            });

        foreach ($exams as $exam) {
            foreach ($exam->users as $user) {
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
            $exam->update([
                'exam_status' => '1'
            ]);
        }
    }
}
