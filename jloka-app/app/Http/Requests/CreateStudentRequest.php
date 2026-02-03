<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudentRequest extends FormRequest
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
            'full_name'     => 'required|string|min:1|max:255',
            'date_of_birth' => 'required|date|before:today|date_format:Y-m-d',
            'email'         => 'required|email|unique:students',
            'phone'         => 'starts_with:09|unique:students|nullable'
        ];
    }
}
