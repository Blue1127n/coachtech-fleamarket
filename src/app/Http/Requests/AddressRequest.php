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
        \Log::info('AddressRequestが呼び出されました');
        return [
            'name' => 'required|string|max:255',
            'postal_code' => 'required|regex:/^9999999$/',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'お名前を入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.size' => '郵便番号は8桁で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
