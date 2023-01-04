<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;


class SellersendCommentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {       
        return [
            'stars' => ['required','integer', Rule::in([1,2,3,4,5])],
            'description' => ['string','max:255'],
            'products_id' => ['required','integer','exists:products,id'],
        ];
        
    }

    protected function failedValidation(Validator $validator)
    {
        $response = [
            'status' => 'failure',
            'status_code' => 400,
            'message' => 'Bad Request',
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, 400));
    }
    

}
