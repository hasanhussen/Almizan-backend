<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'exam_id' => [
                'required',
                'exists:exams,id',
                Rule::unique('user_exam')->where(
                    fn($q) =>
                    $q->where('user_id', $this->user_id)
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'exam_id.unique' => 'This student is already added to this exam.',
        ];
    }
}
