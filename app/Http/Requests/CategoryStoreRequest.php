<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class CategoryStoreRequest extends FormRequest
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
            'name'=>['required','string','max:40'],
            'description'=>['nullable','string','max:300']
        ];
    }

    /* Return validation errors as json response
    *
    * @param Validator $validator
    */
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

