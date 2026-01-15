<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email,',
            'role' => 'sometimes|string|in:admin,supervisor,teacher,student',
            'password' => 'required|string|min:8|confirmed',
            'image'    => 'required_if:role,student|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'year' => 'sometimes|string|in:1st,2nd,3rd,4th',
        ];
    }
}
