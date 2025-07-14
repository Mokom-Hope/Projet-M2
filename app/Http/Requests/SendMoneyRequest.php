<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMoneyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'recipient' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !preg_match('/^\+?[1-9]\d{1,14}$/', $value)) {
                        $fail('Le destinataire doit être un email valide ou un numéro de téléphone valide.');
                    }
                }
            ],
            'amount' => 'required|numeric|min:200|max:1000000',
            'currency' => 'required|string|in:XOF,XAF,USD,EUR',
            'security_question' => 'required|string|min:10|max:255',
            'security_answer' => 'required|string|min:3|max:100',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'notes' => 'nullable|string|max:500'
        ];
    }

    public function messages()
    {
        return [
            'recipient.required' => 'Le destinataire est obligatoire',
            'amount.required' => 'Le montant est obligatoire',
            'amount.min' => 'Le montant minimum est de 200 FCFA',
            'amount.max' => 'Le montant maximum est de 1,000,000 FCFA',
            'currency.required' => 'La devise est obligatoire',
            'security_question.required' => 'La question de sécurité est obligatoire',
            'security_question.min' => 'La question doit contenir au moins 10 caractères',
            'security_answer.required' => 'La réponse de sécurité est obligatoire',
            'security_answer.min' => 'La réponse doit contenir au moins 3 caractères',
            'payment_method_id.required' => 'La méthode de paiement est obligatoire',
            'payment_method_id.exists' => 'Méthode de paiement invalide'
        ];
    }

    protected function prepareForValidation()
    {
        // Nettoyer le numéro de téléphone
        if ($this->recipient && !filter_var($this->recipient, FILTER_VALIDATE_EMAIL)) {
            $this->merge([
                'recipient' => preg_replace('/[^+\d]/', '', $this->recipient)
            ]);
        }

        // Nettoyer le montant
        if ($this->amount) {
            $this->merge([
                'amount' => (float) str_replace([' ', ','], ['', '.'], $this->amount)
            ]);
        }
    }
}
