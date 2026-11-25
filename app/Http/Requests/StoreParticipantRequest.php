<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $targetUserId = $this->input('user_id') ?? Auth::id();

        return [
            'event_id' => [
                'required',
                'integer',
                'exists:events,id',
                // Cegah pendaftaran ganda pada event yang sama
                Rule::unique('participants', 'event_id')
                    ->where('user_id', $targetUserId),
            ],
            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'event_id.unique' => 'Pengguna sudah terdaftar sebagai peserta pada acara ini.',
        ];
    }
}