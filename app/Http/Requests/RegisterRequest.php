<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'telephone' => 'required|string|max:20|unique:users,telephone',
            'email' => 'required|string|email|max:100|unique:users,email',
            'roleId' => 'required|exists:role,id',
        ];
    }

    public function messages()
    {
        return [
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé',
            'email.unique' => 'Cette adresse email est déjà utilisée',
            'roleId.exists' => 'Le rôle sélectionné n\'existe pas'
        ];
    }
}