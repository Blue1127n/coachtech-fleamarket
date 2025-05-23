<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'category' => ['required'],
            'condition' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'image.required' => '商品画像をアップロードしてください',
            'image.image' => '画像ファイルを選択してください',
            'image.mimes' => 'jpegまたはpng形式で登録してください',
            'category.required' => 'カテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'name.required' => '商品名を入力してください',
            'name.max' => '商品名は255文字以内で入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'price.required' => '価格を入力してください',
            'price.numeric' => '価格は数値で入力してください',
            'price.min' => '価格は0円以上にしてください',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'バリデーションエラー',
            'errors' => $validator->errors()
        ], 422));
    }
}

