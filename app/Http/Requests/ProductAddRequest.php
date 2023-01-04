<?php

namespace App\Http\Requests;

use App\Models\Subcategories;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductAddRequest extends FormRequest
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
          'name'            => ['required', 'string' , 'max:50'],
          'price'           => ['required', 'numeric', 'between:0,9999.99'],
          'stock'           => ['required', 'integer', 'min:1'],
          'img_url'         => ['required', 'string' , 'max:500'],
          'description'     => ['required', 'string' , 'max:100'],
          'subcategories_id'=> ['required', 'integer', 'exists:subcategories,id'],
          'promotions_id'   => ['required', 'integer', 'exists:promotions,id'],
          'brands_id'       => ['required', 'integer', 'exists:brands,id'],
          'users_id'        => ['required', 'integer', 'exists:users,id']  
        ]; 
    }

    protected function failedValidation(Validator $validator)
    {
        $response = [
            'code'        => 400,
            'status'      => 'Bad Request',
            "message"     => "The server cannot or will not process the request due to something that is perceived to be a client error.",
            'errors'      => $validator->errors(),
        ];
        throw new HttpResponseException(response()->json($response, 400));
    }
}
