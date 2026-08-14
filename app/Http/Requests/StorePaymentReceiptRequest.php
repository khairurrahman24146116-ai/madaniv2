<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentReceiptRequest extends FormRequest
{
    /**
     * Hanya bendahara yang boleh mencatat pembayaran SPP.
     */
    public function authorize(): bool
    {
        return $this->user()?->isBendahara() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020'],
            'amount' => ['required', 'numeric', 'min:1'],
            'method' => ['required', 'in:cash,transfer,virtual_account,qris'],
            'reference' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
            'proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof.required' => 'Bukti pembayaran (foto kwitansi/bukti transfer) wajib diunggah.',
            'proof.image' => 'Bukti pembayaran harus berupa file gambar.',
            'proof.max' => 'Ukuran bukti pembayaran maksimal 2MB.',
            'method.in' => 'Metode pembayaran tidak valid.',
            'amount.min' => 'Nominal pembayaran minimal Rp 1.',
        ];
    }
}
