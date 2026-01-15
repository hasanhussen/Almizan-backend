<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\AppInfo;
use Illuminate\Http\Request;
use Carbon\Carbon;


class SubjectController extends Controller
{

    public function getSubjects()
    {
        $today = Carbon::today()->toDateString();
        $now   = Carbon::now('Asia/Damascus')->format('H:i:s');

        $subjects = Subject::whereHas('exams', function ($query) use ($today, $now) {
            $query->whereDate('exam_date', $today)
                ->whereTime('start_time', '<=', $now)
                ->whereTime('end_time', '>=', $now);
        })
            ->with([
                'exams' => function ($query) use ($today, $now) {
                    $query->whereDate('exam_date', $today)
                        ->whereTime('start_time', '<=', $now)
                        ->whereTime('end_time', '>=', $now);
                },
                'exams.users',
                'specialties'
            ])
            ->get();

        $whatsapp = AppInfo::first()?->whatsapp ?? '0';

        return response()->json([
            'subjects' => $subjects,
            'whatsapp' => $whatsapp
        ]);
    }
}
