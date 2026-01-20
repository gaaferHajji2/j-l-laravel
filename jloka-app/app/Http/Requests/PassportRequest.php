<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PassportRequest extends FormRequest
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
            'passport_number' => 'required|string|min:5|max:200|unique:passports',
            'issue_date' => 'required|date|date_format:YYYY-mm-dd|after:yesterday',
            'expiry_date' => 'required|date|after:issue_date|date_format:YYYY-mm-dd',
            'country' => 'required|string|min:2|max:200',
            'customer_identifier' => 'required|string|exists:customers,customer_code'
        ];
    }
}
