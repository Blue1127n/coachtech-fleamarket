<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressChangeRequest extends FormRequest
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
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable','string','max:255'],
        ];
    }

    public function messages()
    {
        return [
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号は「XXX-XXXX」の形式の8桁で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('postal_code')) {
            $this->merge([
                'postal_code' => preg_replace('/^(\d{3})(\d{4})$/', '$1-$2', $this->postal_code),
            ]);
        }
    }
}
