<?php

namespace App\Http\Requests;

use App\Enum\EventStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
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
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'location'    => ['sometimes', 'required', 'string', 'max:255'],
            'start_time'  => ['sometimes', 'required', 'date'],
            'end_time'    => ['sometimes', 'nullable', 'date', 'after:start_time'],
            'status'      => ['sometimes', 'required', Rule::enum(EventStatus::class)],
        ];
    }
}
