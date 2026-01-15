<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:subjects,name',
            'year' => 'nullable|in:1st,2nd,3rd,4th',
            'success_rate' => 'nullable|integer|min:0',
            'semester' => 'required|in:first,second',
            'mark' => 'required|numeric|min:0',
            'teachers'   => 'nullable|array',
            'teachers.*' => 'exists:users,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $mark = $this->input('mark');
            $successRate = $this->input('success_rate');

            if ($successRate > $mark) {
                $validator->errors()->add('success_rate', 'The success rate cannot exceed the mark.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Subject name is required.',
            'name.string' => 'Subject name must be a string.',
            'name.max' => 'Subject name must not exceed 255 characters.',
            'name.unique' => 'Subject name must be unique.',
            'year.in' => 'Year must be one of the following: 1st, 2nd, 3rd, 4th.',
            'success_rate.integer' => 'Success rate must be an integer.',
            'success_rate.min' => 'Success rate must be at least 0%.',
            'semester.required' => 'Semester is required.',
            'semester.in' => 'Semester must be either "first" or "second".',
            'mark.required' => 'Mark is required.',
            'mark.numeric' => 'Mark must be a number',
            'mark.min' => 'Mark must be at least 0',
        ];
    }
}
