<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
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
            'teacher_subject_id' => ['required', 'exists:teacher_subjects,id'],
            'day' => ['required', 'in:senin,selasa,rabu,kamis,jumat,sabtu'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'hour_order' => ['required', 'integer', 'min:1', 'max:4'],
        ];
    }
}
