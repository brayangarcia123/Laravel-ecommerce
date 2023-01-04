<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CouponAddRequest extends FormRequest
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
            'code'=> ['required','string','max:100'],
            'start_date'=> ['required','date'],
            'ending_date'=> ['required','date'],
            'type_discount'=> ['required','numeric','between:0,99.99'],
            'description'=> ['string','max:255'],
            'users_id' => ['required','integer','exists:users,id'],
            'cupons_types_id' => ['required','integer','exists:cupons_types,id'],
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
