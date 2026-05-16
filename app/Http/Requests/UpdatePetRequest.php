<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePetRequest extends FormRequest
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
            'species_id' => ['sometimes', 'integer', 'exists:species,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'breed' => ['nullable', 'string', 'max:255'],
            'gender' => ['sometimes', Rule::in(['male', 'female', 'unknown'])],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'color' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'species_id.exists' => 'Loài thú cưng không hợp lệ.',
            'name.string' => 'Tên thú cưng phải là chuỗi ký tự.',
            'gender.in' => 'Giới tính thú cưng không hợp lệ.',
            'birth_date.before_or_equal' => 'Ngày sinh phải là ngày hiện tại hoặc trước đó.',
            'weight.numeric' => 'Cân nặng phải là số.',
        ];
    }
}
