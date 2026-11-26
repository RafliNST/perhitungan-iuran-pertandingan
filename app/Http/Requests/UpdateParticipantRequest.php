<?php

namespace App\Http\Requests;

use App\Enum\PaymentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_status' => ['sometimes', 'required', Rule::enum(PaymentStatus::class)],
            'fee_total'      => ['sometimes', 'required', 'numeric', 'min:0'],
            'attended_at'    => ['sometimes', 'nullable', 'date'],
        ];
    }
}