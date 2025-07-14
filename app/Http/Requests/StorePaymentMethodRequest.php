<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|string|in:mobile_money,bank_transfer,card',
            'provider' => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
            'bank_code' => 'nullable|string|max:20',
            'country_id' => 'required|exists:countries,id',
            'currency' => 'required|string|in:XOF,XAF,USD,EUR',
            'is_default' => 'boolean',
            'metadata' => 'nullable|array'
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'Le type de méthode de paiement est obligatoire',
            'type.in' => 'Type de méthode de paiement invalide',
            'provider.required' => 'Le fournisseur est obligatoire',
            'account_number.required' => 'Le numéro de compte est obligatoire',
            'account_name.required' => 'Le nom du compte est obligatoire',
            'country_id.required' => 'Le pays est obligatoire',
            'country_id.exists' => 'Pays invalide',
            'currency.required' => 'La devise est obligatoire',
            'currency.in' => 'Devise non supportée'
        ];
    }
}
