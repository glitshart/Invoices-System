<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProduct extends FormRequest
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
            'product_name' => ['required', Rule::unique('products')->ignore($this->id)],
            'section_id' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return  [
            'product_name.required' => 'يرجي ادخال اسم المنتج',
            'product_name.unique' => 'اسم المنتج مسجل مسبقا',
            'section_id.required' => 'يرجي ادخال القسم لهذا المنتج',
        ];
    }
}
