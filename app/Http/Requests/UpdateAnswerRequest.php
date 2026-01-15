<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnswerRequest extends FormRequest
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
            'answers' => 'required|array|min:1',
            'answers.*' => 'required|string',
            'is_correct' => 'required|array|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'is_correct.required' => ' يجب تحديد إجابة صحيحة على الأقل  ',
            'is_correct.array' => ' يجب تحديد إجابة صحيحة على الأقل  ',
            'is_correct.min' => ' يجب تحديد إجابة صحيحة على الأقل  ',
        ];
    }
}
