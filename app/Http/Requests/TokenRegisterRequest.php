<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TokenRegisterRequest extends FormRequest
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
            'firstname' => ['max:50'],
            'lastname' => ['max:50'],
            'username' => ['max:50'],
            'email' => ['required','email','unique:users,email','max:50'],
            'telephone' => ['max:9'],
            'direction' => ['max:50'],
            'document' => ['max:100'],
            'type_document' => ['max:20'],
            'password' => ['required','max:100'],
        ];
    }
}
