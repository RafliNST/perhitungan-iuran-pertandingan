<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Override;

class StoreCommunityMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Gunakan $this->user()?->id agar selalu terbaca saat memakai Sanctum::actingAs()
        $targetUserId = $this->input('user_id') ?? $this->user()?->id;

        return [
            'community_id' => [
                'required',
                'integer',
                'exists:communities,id',
                Rule::unique('community_members', 'community_id')
                    ->where('user_id', $targetUserId),
            ],
            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    #[Override]
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 422));
    }
}
