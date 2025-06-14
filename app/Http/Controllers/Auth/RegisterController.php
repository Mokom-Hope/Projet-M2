<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    protected $redirectTo = '/login';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegistrationForm()
    {
        $countries = Country::orderBy('name', 'asc')->get();
        return view('auth.register', compact('countries'));
    }

    /**
     * Gérer l'inscription
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        event(new Registered($user = $this->create($request->all())));

        return redirect($this->redirectTo)->with('success', 'Inscription réussie ! Veuillez vous connecter.');
    }

    /**
     * Valider les données d'inscription
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'country_code' => ['required', 'string', 'max:3', 'exists:countries,code'],
            'currency' => ['nullable', 'string', 'max:3'],
        ]);
    }

    /**
     * Créer un nouvel utilisateur
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'country_code' => $data['country_code'],
            'currency' => $data['currency'] ?? 'XOF',
        ]);
    }
}