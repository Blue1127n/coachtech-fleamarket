<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;

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
            'email' => ['required', 'string', 'max:255'],
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

    protected function prepareForValidation()
    {
        $this->merge([
            'email' => trim($this->email),
        ]);
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $value = $this->input('email');

            if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !User::where('name', $value)->exists()) {
                $validator->errors()->add('email', '正しいメールアドレスの形式で入力するか、登録済みのユーザー名を入力してください。');
            }
        });
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'バリデーションエラー',
            'errors' => $validator->errors()
        ], 422));
    }

    public function expectsJson()
    {
        return true;
    }
}

