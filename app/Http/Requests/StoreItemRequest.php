<?php

namespace App\Http\Requests;

use App\Enum\ItemDistributionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id'          => ['required', 'integer', 'exists:events,id'],
            'name'              => ['required', 'string', 'max:255'],
            'price'             => ['required', 'numeric', 'min:0'],
            'distribution_type' => ['required', Rule::enum(ItemDistributionType::class)],
        ];
    }
}