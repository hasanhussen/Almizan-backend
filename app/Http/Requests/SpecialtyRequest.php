<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpecialtyRequest extends FormRequest
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
        $specialtyId = optional($this->route('specialty'))->id;
        return [
            'name' => 'required|string|max:255|unique:specialties,name,' . $specialtyId,
            'student_count' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'اسم الاختصاص مستخدم من قبل. يرجى اختيار اسم مختلف.',
            'name.required' => ' يجب  ادخال اسم الاختصاص ',
            'name.string' => 'اسم الاختصاص يجب أن يكون نصًا.',
            'name.max' => 'اسم الاختصاص يجب ألا يتجاوز 255 حرفًا.',
            'student_count.required' => ' يجب  ادخال عدد الطلاب ',
            'student_count.integer' => 'عدد الطلاب يجب أن يكون رقمًا صحيحًا.',
            'student_count.min' => 'عدد الطلاب يجب أن يكون صفر أو أكثر.',
        ];
    }
}
