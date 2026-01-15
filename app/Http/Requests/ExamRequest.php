<?php

namespace App\Http\Requests;

use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
            'total_marks' => 'required|integer|min:0',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'actual_duration' => 'required|integer|min:1',
            'term_name' => 'required|in:First Semester,Second Semester,Supplementary',
            'season'    => 'required|string|max:15',
            'exam_type' => 'required|in:quiz,midterm,final,assignment,project,participation,oral,practice,makeup',
            'exam_date' => 'required|date',
            'success_rate' => 'required|numeric|min:0',
            'year' => 'required|in:1st,2nd,3rd,4th',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $totalMarks = $this->input('total_marks');
            $successRate = $this->input('success_rate');

            if ($successRate > $totalMarks) {
                $validator->errors()->add('success_rate', 'The success rate cannot exceed the total marks.');
            }

            $subject = Subject::find($this->subject_id);

            if ($subject && $subject->year !== $this->year) {
                $validator->errors()->add(
                    'subject_id',
                    'السنة المختارة لا تطابق السنة الخاصة بالمادة.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'subject_id.required' => ' يجب اختيار المادة ',
            'subject_id.exists' => 'المادة المختارة غير صالحة.',
            'total_marks.required' => ' يجب  ادخال العلامة الكلية ',
            'total_marks.integer' => ' العلامة الكلية يجب أن تكون رقمًا صحيحًا.',
            'total_marks.min' => ' العلامة الكلية يجب أن تكون صفر أو أكثر.',
            'start_time.required' => ' يجب  ادخال وقت البداية ',
            'start_time.date_format' => 'وقت البداية يجب أن يكون بالتنسيق الصحيح (HH:MM:SS).',
            'end_time.required' => ' يجب  ادخال وقت النهاية ',
            'end_time.date_format' => 'وقت النهاية يجب أن يكون بالتنسيق الصحيح (HH:MM:SS).',
            'end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية.',
            'actual_duration.required' => ' يجب  ادخال المدة الفعلية ',
            'actual_duration.integer' => 'المدة الفعلية يجب أن تكون رقمًا صحيحًا.',
            'actual_duration.min' => 'المدة الفعلية يجب أن تكون دقيقة واحدة على الأقل.',
            'term_name.in' => 'الدورة الامتحانية  غير صالحة',
            'term_name.required' => 'يجب اختيار الدورة الامتحانية',
            'season.required'    => 'يجب اختيار الموسم الدراسي',
            'season.max' => 'الفصل الدراسي يجب ألا يتجاوز 255 حرفًا.',
            'exam_date.required' => ' يجب  ادخال تاريخ الامتحان ',
            'exam.required' => ' يجب  ادخال نوع الامتحان ',
            'exam_date.date' => 'تاريخ الامتحان يجب أن يكون تاريخًا صالحًا.',
            'exam_type.required' => ' يجب  ادخال نوع الامتحان ',
            'exam_type.in' => 'نوع الامتحان المختار غير صالح.',
            'success_rate.required' => ' يجب  ادخال نسبة النجاح ',
            'success_rate.numeric' => 'نسبة النجاح يجب أن تكون رقمًا.',
            'success_rate.min' => 'نسبة النجاح لا يمكن أن تكون أقل من 0%.',
            'success_rate.max' => 'نسبة النجاح لا يمكن أن تتجاوز 100%.',
            'year.required' => ' يجب اختيار السنة الدراسية ',
            'year.in' => 'السنة الدراسية المختارة غير صالحة.',
        ];
    }
}
