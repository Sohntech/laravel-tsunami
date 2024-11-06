<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'telephone' => 'required|string|exists:users,telephone',
            'montant' => 'required|numeric|min:500',
        ];
    }

    public function messages()
    {
        return [
            'telephone.exists' => 'Ce numéro n\'est pas inscrit sur Wave',
            'montant.min' => 'Le montant minimum est de 500 FCFA',
            'montant.numeric' => 'Le montant doit être un nombre'
        ];
    }
}