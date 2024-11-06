<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class ScheduleTransferRequest extends FormRequest
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
            'frequence' => 'required|in:daily,weekly,monthly',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'nullable|date|after:date_debut',
            'heure_execution' => 'required|date_format:H:i'
        ];
    }

    public function messages()
    {
        return [
            'telephone.exists' => 'Ce numéro n\'est pas inscrit sur Wave',
            'montant.min' => 'Le montant minimum est de 500 FCFA',
            'frequence.in' => 'La fréquence doit être daily, weekly ou monthly',
            'date_debut.after_or_equal' => 'La date de début doit être aujourd\'hui ou une date future',
            'date_fin.after' => 'La date de fin doit être après la date de début'
        ];
    }
}