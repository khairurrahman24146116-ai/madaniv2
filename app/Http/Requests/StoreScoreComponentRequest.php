<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreScoreComponentRequest extends FormRequest
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
            'subject_id' => ['required', 'exists:subjects,id'],
            'code' => ['required', 'in:tugas,ph,uts,uas'],
            'name' => ['required'],
            'weight' => ['required', 'numeric', 'min:0', 'max:100'],
            'semester' => ['required', 'in:ganjil,genap'],
            'academic_year' => ['required'],
        ];
    }
}
