<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code_marchand' => 'required|string|exists:users,code',
            'montant' => 'required|numeric|min:100',
            'description' => 'nullable|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'code_marchand.exists' => 'Code marchand invalide',
            'montant.min' => 'Le montant minimum est de 100 FCFA'
        ];
    }
}