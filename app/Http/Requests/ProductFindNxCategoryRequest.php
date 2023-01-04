<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ProductFindNxCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {       
        return [
            'category' => ['required','string'],
            'quantity' => ['integer', 'min:1']
        ];
        
    }

    protected function failedValidation(Validator $validator)
    {
        $response = [
            'code'    => 400,
            'status'  => 'Bad Request',
            "message" => "The server cannot or will not process the request due to something that is perceived to be a client error.",
            'errors'  => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, 400));
    }
}
