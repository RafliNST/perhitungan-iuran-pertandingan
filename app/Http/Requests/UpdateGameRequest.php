<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $game = $this->route('game');
        $eventId = $game?->event_id;
        $teamId = $this->input('team_id', $game?->team_id);

        return [
            'team_id' => ['sometimes', 'required', 'integer', 'min:1'],
            'user_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('participants', 'user_id')->where('event_id', $eventId),
                Rule::unique('games', 'user_id')
                    ->where('team_id', $teamId)
                    ->where('event_id', $eventId)
                    ->ignore($game?->id),
            ],
        ];
    }
}