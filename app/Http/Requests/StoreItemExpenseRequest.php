<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId = $this->input('event_id');

        return [
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'item_id'  => [
                'required',
                'integer',
                Rule::exists('items', 'id')->where('event_id', $eventId),
            ],
            'user_id'  => [
                'required',
                'integer',
                Rule::exists('participants', 'user_id')->where('event_id', $eventId),
            ],
            'qty'      => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.exists' => 'Item tidak ditemukan pada acara ini.',
            'user_id.exists' => 'Pengguna tidak terdaftar sebagai peserta pada acara ini.',
        ];
    }
}