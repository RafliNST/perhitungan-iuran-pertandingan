<?php

namespace App\Http\Requests;

use App\Enum\ItemDistributionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['sometimes', 'required', 'string', 'max:255'],
            'price'             => ['sometimes', 'required', 'numeric', 'min:0'],
            'distribution_type' => ['sometimes', 'required', Rule::enum(ItemDistributionType::class)],
        ];
    }
}