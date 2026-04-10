<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class StoreOwnerAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        return [
            'pet_id' => ['required', 'integer', 'exists:pets,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ];
    }
}