<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddFavoriRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'telephone' => 'required|string|exists:users,telephone',
            'alias' => 'nullable|string|max:50'
        ];
    }

    public function messages()
    {
        return [
            'telephone.exists' => 'Ce numéro n\'est pas inscrit sur Wave',
            'alias.max' => 'L\'alias ne doit pas dépasser 50 caractères'
        ];
    }
}