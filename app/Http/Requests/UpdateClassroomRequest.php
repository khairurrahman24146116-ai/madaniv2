<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'grade' => ['required', 'in:X,XI,XII'],
            'academic_year' => ['required'],
            'description' => ['nullable'],
            'wali_kelas_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
