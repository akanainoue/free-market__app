<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatMessageRequest extends FormRequest
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
            'message' => 'nullable|required_without:image|string|max:400',
            'image' => 'nullable|image|mimes:jpeg,png|max:2048', // 拡張子 & サイズ制限
        ];
    }

    public function messages(): array
    {
        return [
            'message.required_without' => '本文を入力してください',
            'message.max' => '本文は400文字以内で入力してください',
            // 'image.image' => '画像ファイルをアップロードしてください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.max' => '画像サイズは2MB以下にしてください',
        ];
    }
}
