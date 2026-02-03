<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAuthorRequest extends FormRequest
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
            'author_slug'   => 'required|unique:authors|min:1|max:255|string',
            'name'          => 'required|string|min:1|max:255',
            'email'         => 'required|email|unique:authors',
            'bio'           => 'required|string|min:1|max:50000|nullable'
        ];
    }
}
