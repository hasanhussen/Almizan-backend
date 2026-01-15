<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;

class SmartQuestionAnswerSeeder extends Seeder
{
    public function run()
    {
        $exam = Exam::with('subject')->first();
        if (!$exam) return;

        $questionsBank = $this->getQuestionsBySubject($exam->subject->name);

        foreach ($questionsBank as $data) {

            $mark = match ($data['difficulty']) {
                'easy' => 1,
                'medium' => 2,
                'hard' => 3,
            };

            $question = Question::create([
                'exam_id' => $exam->id,
                'question_text' => $data['question'],
                'mark' => $mark,
            ]);

            foreach ($data['answers'] as $answer) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $answer['text'],
                    'is_correct' => $answer['correct'],
                ]);
            }
        }
    }

    private function getQuestionsBySubject(string $subjectName): array
    {
        if ($subjectName === 'محاسبة مالية') {
            return [
                [
                    'question' => 'ما هو الهدف الأساسي من المحاسبة المالية؟',
                    'difficulty' => 'easy',
                    'answers' => [
                        ['text' => 'تسجيل وتحليل العمليات المالية', 'correct' => true],
                        ['text' => 'إدارة الموارد البشرية', 'correct' => false],
                        ['text' => 'تحديد الرواتب', 'correct' => false],
                        ['text' => 'التسويق والمبيعات', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'أي مما يلي يُعتبر من عناصر الميزانية العمومية؟',
                    'difficulty' => 'medium',
                    'answers' => [
                        ['text' => 'الأصول', 'correct' => true],
                        ['text' => 'الإيرادات', 'correct' => false],
                        ['text' => 'المصروفات', 'correct' => false],
                        ['text' => 'الأرباح التشغيلية', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'ما هو القيد المحاسبي الصحيح عند شراء أصل نقدًا؟',
                    'difficulty' => 'hard',
                    'answers' => [
                        ['text' => 'مدين أصل / دائن صندوق', 'correct' => true],
                        ['text' => 'مدين صندوق / دائن أصل', 'correct' => false],
                        ['text' => 'مدين مصروف / دائن أصل', 'correct' => false],
                        ['text' => 'مدين إيراد / دائن صندوق', 'correct' => false],
                    ],
                ],
            ];
        }

        // افتراضي لأي مادة أخرى
        return [
            [
                'question' => 'ما هو مفهوم الإدارة؟',
                'difficulty' => 'easy',
                'answers' => [
                    ['text' => 'تنظيم وتوجيه الموارد لتحقيق الأهداف', 'correct' => true],
                    ['text' => 'إدارة الحسابات فقط', 'correct' => false],
                    ['text' => 'إعداد التقارير المالية', 'correct' => false],
                    ['text' => 'تحليل البيانات المحاسبية', 'correct' => false],
                ],
            ],
        ];
    }
}

