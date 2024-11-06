<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'telephone' => 'required|string',
            'code' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'telephone.required' => 'Le numéro de téléphone est requis',
            'code.required' => 'Le code est requis'
        ];
    }
}