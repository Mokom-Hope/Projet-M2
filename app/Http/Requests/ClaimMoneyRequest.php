<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimMoneyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'transfer_code' => 'required|string|size:8|exists:transfers,transfer_code',
            'security_answer' => 'required|string|min:3|max:100',
            'recipient_identifier' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !preg_match('/^\+?[1-9]\d{1,14}$/', $value)) {
                        $fail('L\'identifiant doit être un email valide ou un numéro de téléphone valide.');
                    }
                }
            ],
            'payment_method_type' => 'required|string|in:mobile_money,bank_transfer,card',
            'payment_method_provider' => 'required|string',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
            'country_id' => 'required|exists:countries,id'
        ];
    }

    public function messages()
    {
        return [
            'transfer_code.required' => 'Le code de transfert est obligatoire',
            'transfer_code.size' => 'Le code de transfert doit contenir 8 caractères',
            'transfer_code.exists' => 'Code de transfert invalide',
            'security_answer.required' => 'La réponse de sécurité est obligatoire',
            'recipient_identifier.required' => 'Votre identifiant (email ou téléphone) est obligatoire',
            'payment_method_type.required' => 'Le type de méthode de paiement est obligatoire',
            'payment_method_provider.required' => 'Le fournisseur de paiement est obligatoire',
            'account_number.required' => 'Le numéro de compte est obligatoire',
            'account_name.required' => 'Le nom du compte est obligatoire',
            'country_id.required' => 'Le pays est obligatoire'
        ];
    }

    protected function prepareForValidation()
    {
        // Nettoyer l'identifiant
        if ($this->recipient_identifier && !filter_var($this->recipient_identifier, FILTER_VALIDATE_EMAIL)) {
            $this->merge([
                'recipient_identifier' => preg_replace('/[^+\d]/', '', $this->recipient_identifier)
            ]);
        }

        // Convertir en majuscules le code de transfert
        if ($this->transfer_code) {
            $this->merge([
                'transfer_code' => strtoupper($this->transfer_code)
            ]);
        }
    }
}
