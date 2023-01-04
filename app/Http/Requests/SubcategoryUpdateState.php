<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class SubcategoryUpdateState extends FormRequest
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
            'is_actived'=>['required','boolean']
        ];
    }
    public function messages()
    {
        return [
            'state.required'=>'A state is required',
            'state.boolean' =>'The state must be 0 or 1'
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $response = [
            'state' => 'failure',
            'message' => 'Bad Request',
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, 400));
    }
}
