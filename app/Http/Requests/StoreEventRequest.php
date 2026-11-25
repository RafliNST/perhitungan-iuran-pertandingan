<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'location'     => ['required', 'string', 'max:255'],
            'start_time'   => ['required', 'date', 'after_or_equal:now'],
            'end_time'     => ['nullable', 'date', 'after:start_time'],
            'community_id' => ['nullable', 'integer', 'exists:communities,id'],
        ];
    }
}
