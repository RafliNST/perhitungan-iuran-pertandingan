<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Override;

class StoreCommunityRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255', 'min:1'],
            'community_banner' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama komunitas harus diisi.',
            'name.string' => 'Nama komunitas harus berupa teks.',
            'name.max' => 'Nama komunitas tidak boleh lebih dari 255 karakter.',
            'name.min' => 'Nama komunitas harus memiliki setidaknya 1 karakter.',
            'community_banner.image' => 'Banner komunitas harus berupa gambar.',
            'community_banner.max' => 'Banner komunitas tidak boleh lebih dari 5MB.',
            'community_banner.mimes' => 'Banner komunitas harus berupa file JPEG, PNG, atau JPG.',
            'description.string' => 'Deskripsi komunitas harus berupa teks.',
        ];
    }

    #[Override]
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag(),
        ], 422));
    }
}
