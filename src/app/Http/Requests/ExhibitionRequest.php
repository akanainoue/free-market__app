<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'image' => ['required', 'image', 'mimes:jpeg,png'],
            'category_id' => ['required', 'exists:categories,id'],
            'condition_id' => ['required', 'exists:conditions,id'],
            'name' => ['required', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '商品名は入力必須です。',
            'description.required' => '商品説明は入力必須です。',
            'description.max' => '商品説明は255文字以内で入力してください。',
            'image.required' => '商品画像はアップロード必須です。',
            'image.mimes' => '商品画像は.jpegまたは.png形式でアップロードしてください。',
            'category_id.required' => '商品のカテゴリーを選択してください。',
            'condition_id.required' => '商品の状態を選択してください。',
            'price.required' => '商品価格は入力必須です。',
            'price.integer' => '商品価格は数値で入力してください。',
            'price.min' => '商品価格は0円以上で入力してください。',
        ];
    }
}
