<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Specialty;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use App\Models\UserExam;
use App\Models\UserAnswer;
use App\Models\Result;
use App\Models\SubjectResult;

class MySeeder extends Seeder
{
    public function run()
    {
        // 5 تخصصات
        $specialties = Specialty::factory(5)->create();

        // 20 مادة
        $subjects = Subject::factory(20)->create();

        // اربط المواد بالتخصصات
        foreach($specialties as $specialty){
            $specialty->subjects()->attach($subjects->random(5)->pluck('id'));
        }

        // 50 طالب
        $users = User::factory(50)->create();

        // لكل مادة 2 امتحانات
        $exams = collect();
        foreach($subjects as $subject){
            $exams = $exams->merge(Exam::factory(2)->create(['subject_id'=>$subject->id]));
        }

        // لكل امتحان 5-10 أسئلة
        foreach($exams as $exam){
            $questions = Question::factory(rand(5,10))->create(['exam_id'=>$exam->id]);
            foreach($questions as $question){
                // لكل سؤال 4 أجوبة
                // $answers = Answer::factory(4)->create(['question_id'=>$question->id]);
                // // اختار جواب واحد صحيح
                // $correct = $answers->random();
                // $correct->update(['is_correct'=>true]);

                $data = $this->generateAnswersForQuestion($question->question_text);

                // الجواب الصحيح
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $data['correct'],
                    'is_correct' => true,
                ]);

                // الأجوبة الخاطئة
                foreach ($data['wrong'] as $wrong) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $wrong,
                        'is_correct' => false,
                    ]);
                }

            }
        }

        // سجل كل الطلاب في كل الامتحانات
        foreach($users as $user){
            foreach($exams as $exam){
                $userExam = UserExam::factory()->create([
                    'user_id' => $user->id,
                    'exam_id' => $exam->id,
                    'is_submitted' => $exam->exam_status
                    
                ]);

                // أجب عن الأسئلة
                $questions = $exam->questions;
                foreach($questions as $question){
                    $correctAnswer = $question->answers->where('is_correct', true)->first();
                    // اختار اجابة عشوائية (احياناً صحيحة)
                    $chosenAnswer = $question->answers->random();
                    UserAnswer::factory()->create([
                        'user_id' => $user->id,
                        'exam_id' => $exam->id,
                        'question_id' => $question->id,
                        'answer_id' => $chosenAnswer->id,
                    ]);
                }

                // احسب نتيجة الامتحان
                $examDegree = 0;
                foreach($exam->questions as $question){
                    $ua = UserAnswer::where('user_id',$user->id)
                        ->where('question_id',$question->id)
                        ->first();
                    if($ua && $ua->answer->is_correct){
                        $examDegree += $question->mark;
                    }
                }
                $isPassed = $examDegree >= ($exam->success_rate/100 * $exam->total_marks);

                Result::create([
                    'user_id' => $user->id,
                    'subject_id' => $exam->subject_id,
                    'exam_id' => $exam->id,
                    'exam_degree' => $examDegree,
                    'is_passed' => $isPassed,
                ]);
            }

            // احسب نتيجة المادة
            foreach($subjects as $subject){
                $totalDegree = Result::where('user_id',$user->id)
                    ->where('subject_id',$subject->id)
                    ->sum('exam_degree');

                SubjectResult::create([
                    'user_id' => $user->id,
                    'subject_id' => $subject->id,
                    'total_degree' => $totalDegree,
                ]);
            }
        }
    }

    private function generateAnswersForQuestion($questionText)
{
    $map = [
        'opportunity cost' => [
            'correct' => 'The value of the next best alternative that is forgone.',
            'wrong' => [
                'The total cost of production.',
                'The market price of a good.',
                'Government spending on goods.',
            ],
        ],
        'microeconomics and macroeconomics' => [
            'correct' => 'Microeconomics studies individual markets, while macroeconomics studies the whole economy.',
            'wrong' => [
                'Both study only global trade.',
                'Macroeconomics focuses on individual consumers.',
                'Microeconomics studies inflation only.',
            ],
        ],
        'GDP' => [
            'correct' => 'The total value of goods and services produced within a country.',
            'wrong' => [
                'Total population income.',
                'Government budget surplus.',
                'Total exports only.',
            ],
        ],
        'law of demand' => [
            'correct' => 'Quantity demanded decreases as price increases, ceteris paribus.',
            'wrong' => [
                'Demand increases when price increases.',
                'Supply decreases when price decreases.',
                'Price is unrelated to demand.',
            ],
        ],
        'inflation' => [
            'correct' => 'A general increase in prices over time.',
            'wrong' => [
                'A decrease in unemployment.',
                'An increase in production.',
                'A reduction in taxes.',
            ],
        ],
    ];

    foreach ($map as $keyword => $answers) {
        if (str_contains(strtolower($questionText), $keyword)) {
            return $answers;
        }
    }

    // fallback
    return [
        'correct' => 'Correct economic definition.',
        'wrong' => [
            'Incorrect explanation.',
            'Unrelated concept.',
            'Wrong definition.',
        ],
    ];
}

}
