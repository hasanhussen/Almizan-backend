<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
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
        $userId = optional($this->route('user'))->id;
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email,' . $userId,
            'role' => 'sometimes|string|in:admin,supervisor,teacher,student',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'year' => 'sometimes|string|in:1st,2nd,3rd,4th',
        ];
    }
}
