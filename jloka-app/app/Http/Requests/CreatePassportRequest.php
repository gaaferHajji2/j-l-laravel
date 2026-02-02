<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePassportRequest extends FormRequest
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
            "passport_number"   => 'required|min:1|max:255|unique:passports',
            "issue_date"        => 'required|date|date_format:Y-m-d|after:yesterday',
            "expiry_date"       => 'required|date|date_format:Y-m-d|after:issue_date',
            "country"           => 'required|string|min:1|max:255',
            "customer_identifier" => "required|string|min:1|max:255|exists:customers,customer_code"
        ];
    }
}
