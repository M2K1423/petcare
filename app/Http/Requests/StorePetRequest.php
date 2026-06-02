<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePetRequest extends FormRequest
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
            'species_id' => ['required', 'integer', 'exists:species,id'],
            'name' => ['required', 'string', 'max:255'],
            'breed' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'unknown'])],
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
            'species_id.required' => 'Vui lòng chọn loài cho thú cưng.',
            'species_id.exists' => 'Loài thú cưng không hợp lệ.',
            'name.required' => 'Vui lòng nhập tên thú cưng.',
            'gender.in' => 'Giới tính thú cưng không hợp lệ.',
            'birth_date.before_or_equal' => 'Ngày sinh phải là ngày hiện tại hoặc trước đó.',
            'weight.numeric' => 'Cân nặng phải là số.',
            'weight.min' => 'Cân nặng không được nhỏ hơn 0.',
        ];
    }
}
