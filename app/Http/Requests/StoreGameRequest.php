<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId = $this->input('event_id');

        return [
            'team_id'      => ['required', 'integer', 'min:1'],
            'event_id'     => ['required', 'integer', 'exists:events,id'],
            'community_id' => ['nullable', 'integer', 'exists:communities,id'],
            'user_id'      => [
                'required',
                'integer',
                Rule::exists('participants', 'user_id')->where('event_id', $eventId),
                Rule::unique('games', 'user_id')
                    ->where('team_id', $this->input('team_id'))
                    ->where('event_id', $eventId),
            ],
        ];
    }
}