<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelTransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'transaction_id' => 'required|exists:transaction,id',
            'reason' => 'required|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'transaction_id.exists' => 'Transaction introuvable',
            'reason.required' => 'La raison d\'annulation est requise'
        ];
    }
}