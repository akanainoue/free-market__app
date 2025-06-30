<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string'],
            'address' => ['required', 'string'],
            'building_name' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザー名を入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'address.required' => '住所を入力してください',
            'profile_image.mimes' => 'プロフィール画像は.jpegまたは.png形式でアップロードしてください。',
        ];
    }
}
