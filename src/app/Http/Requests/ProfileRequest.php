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
            'profile_image' => ['nullable', 'mimes:jpeg,png'],
        ];
    }

    public function messages()
    {
        \Log::info('バリデーション実行', ['data' => $this->all()]);

        return [
            'profile_image.mimes' => 'jpegまたはpng形式で登録してください',
        ];
    }

    public function __construct()
    {
    \Log::info('ProfileRequest Constructed');
    }
}
