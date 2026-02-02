<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetAllPassportDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->passport_uid,
            'passport_number' => $this->passport_number,
            'issue_date' => $this->issue_date,
            'expiry_date' => $this->expiry_date,
            'country' => $this->country,
            'customer_identifier' => $this->customer_identifier,
        ];
    }
}
