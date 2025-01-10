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
        \Log::info('ProfileRequestが呼び出されました');
        return [
            'profile_image' => 'nullable|image|mimes:jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'profile_image.image' => 'プロフィール画像はjpegまたはpng形式でアップロードしてください',
        ];
    }
}
