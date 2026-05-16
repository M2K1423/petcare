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

    public function messages(): array
    {
        return [
            'pet_id.required' => 'Vui lòng chọn thú cưng.',
            'pet_id.exists' => 'Thú cưng không hợp lệ.',
            'appointment_date.required' => 'Vui lòng chọn ngày hẹn.',
            'appointment_date.after_or_equal' => 'Ngày hẹn không được nhỏ hơn hôm nay.',
            'appointment_time.required' => 'Vui lòng chọn giờ hẹn.',
            'appointment_time.date_format' => 'Giờ hẹn phải theo định dạng HH:mm.',
        ];
    }
}