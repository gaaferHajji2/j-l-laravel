<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
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
            'title'             => 'required|string|min:1|max:255',
            'isbn'              => 'required|string|min:1|max:255|unique:books',
            'published_date'    => 'required|date|date_format:Y-m-d|after:yesterday',
            'writer_id'         => 'required|string|min:1|max:255|exists:authors,author_slug'
        ];
    }
}
