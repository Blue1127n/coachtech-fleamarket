<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'ユーザー名またはメールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
        ];
    }

    protected function failedValidation(Validator $validator)
{
    \Log::error('バリデーションエラー', [
        'errors' => $validator->errors(),
        'request' => request()->all(),
    ]);

    if ($this->expectsJson()) {
        throw new ValidationException($validator, response()->json([
            'message' => 'バリデーションエラーがあります',
            'errors' => $validator->errors()
        ], 422));
    }

    throw new ValidationException($validator, redirect()->back()->withErrors($validator)->withInput());
}

}



