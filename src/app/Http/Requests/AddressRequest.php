<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'delivery_postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'delivery_address' => ['required', 'string'],
            'delivery_building_name' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'delivery_postal_code.required' => '郵便番号は入力必須です。',
            'delivery_postal_code.regex' => '郵便番号はハイフンありの8文字（例：123-4567）で入力してください。',
            'delivery_address.required' => '住所は入力必須です。',
        ];
    }
}
