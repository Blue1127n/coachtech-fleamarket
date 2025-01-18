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
        \Log::info('ProfileRequest Validation Rules Triggered', ['data' => $this->all()]);

        return [
        'profile_image' => 'nullable|image|mimes:jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        \Log::info('ProfileRequest Messages Triggered', ['data' => $this->all()]);

        return [
            'profile_image.image' => 'プロフィール画像はjpegまたはpng形式でアップロードしてください',
            'profile_image.mimes' => 'プロフィール画像はjpegまたはpng形式でアップロードしてください',
        ];
    }

    public function __construct()
{
    \Log::info('ProfileRequest Constructed');
}
}
