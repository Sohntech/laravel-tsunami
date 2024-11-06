<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionHistoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'type' => 'nullable|exists:type,id',
            'montant_min' => 'nullable|numeric|min:0',
            'montant_max' => 'nullable|numeric|gt:montant_min',
            'status' => 'nullable|in:completed,cancelled',
            'per_page' => 'nullable|integer|min:1|max:100',
            'telephone' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'end_date.after_or_equal' => 'La date de fin doit être après la date de début',
            'montant_max.gt' => 'Le montant maximum doit être supérieur au montant minimum'
        ];
    }
}