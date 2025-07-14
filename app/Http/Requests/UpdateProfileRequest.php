<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->user()->id;

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($userId)
            ],
            'country_code' => 'required|string|exists:countries,code',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire',
            'last_name.required' => 'Le nom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.unique' => 'Cet email est déjà utilisé',
            'phone.required' => 'Le numéro de téléphone est obligatoire',
            'phone.unique' => 'Ce numéro est déjà utilisé',
            'country_code.required' => 'Le pays est obligatoire',
            'profile_photo.image' => 'Le fichier doit être une image',
            'profile_photo.mimes' => 'L\'image doit être au format JPEG, PNG ou JPG',
            'profile_photo.max' => 'L\'image ne doit pas dépasser 2MB'
        ];
    }
}
