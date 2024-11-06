<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MultipleTransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'telephones' => 'required|array|min:2',
            'telephones.*' => 'required|string|exists:users,telephone',
            'montant' => 'required|numeric|min:500',
        ];
    }

    public function messages()
    {
        return [
            'telephones.required' => 'La liste des numéros est requise',
            'telephones.array' => 'Les numéros doivent être fournis dans un tableau',
            'telephones.min' => 'Vous devez spécifier au moins 2 numéros',
            'telephones.*.exists' => 'Un des numéros n\'est pas inscrit sur Wave',
            'montant.min' => 'Le montant minimum est de 500 FCFA par transfert'
        ];
    }
}