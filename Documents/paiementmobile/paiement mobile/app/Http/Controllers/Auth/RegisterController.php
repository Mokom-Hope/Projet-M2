<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Country;
use App\Services\EmailVerificationService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    // use RegistersUsers; // Removed as per update

    // protected $redirectTo = '/dashboard'; // Removed as per update
    protected $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->middleware('guest');
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegistrationForm()
    {
        $countries = Country::orderBy('name')->get();
        return view('auth.register', compact('countries'));
    }

    /**
     * Traiter l'inscription (étape 1 - envoi du code)
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        // Préparer les données utilisateur pour le cache
        $userData = $request->only([
            'first_name', 'last_name', 'email', 'phone', 
            'country_code', 'currency', 'password'
        ]);
        
        // Hasher le mot de passe avant de le stocker dans le cache
        $userData['password'] = Hash::make($userData['password']);

        // Envoyer le code de vérification
        $result = $this->emailVerificationService->sendVerificationCode(
            $request->email, 
            $userData
        );

        if ($result['success']) {
            return redirect()->route('register.verify', ['email' => base64_encode($request->email)])
                           ->with('success', $result['message']);
        } else {
            return back()->withErrors(['email' => $result['message']])
                        ->withInput();
        }
    }

    /**
     * Afficher la page de vérification du code
     */
    public function showVerificationForm(Request $request)
    {
        $email = base64_decode($request->email);
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('register')->withErrors(['email' => 'Email invalide.']);
        }

        // Vérifier s'il y a une vérification en cours
        $verificationInfo = $this->emailVerificationService->getVerificationInfo($email);
        
        if (!$verificationInfo) {
            return redirect()->route('register')
                           ->withErrors(['email' => 'Aucune vérification en cours. Veuillez vous inscrire à nouveau.']);
        }

        return view('auth.verify-email-code', [
            'email' => $email,
            'verification_info' => $verificationInfo
        ]);
    }

    /**
     * Vérifier le code et créer le compte
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);

        $result = $this->emailVerificationService->verifyCode(
            $request->email, 
            $request->code
        );

        if (!$result['success']) {
            return back()->withErrors(['code' => $result['message']])
                        ->withInput();
        }

        // Créer l'utilisateur avec les données du cache
        $userData = $result['user_data'];
        
        try {
            $user = User::create([
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'country_code' => $userData['country_code'],
                'currency' => $userData['currency'],
                'password' => $userData['password'], // Déjà hashé
                'email_verified_at' => now(),
            ]);

            event(new Registered($user));

            // Connecter l'utilisateur
            auth()->login($user);

            return redirect('/dashboard') // Replaced $this->redirectPath() with hardcoded value
                           ->with('success', 'Compte créé avec succès ! Bienvenue sur MoneyTransfer.');

        } catch (\Exception $e) {
            return back()->withErrors(['code' => 'Erreur lors de la création du compte. Veuillez réessayer.'])
                        ->withInput();
        }
    }

    /**
     * Renvoyer un code de vérification
     */
    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Récupérer les données de vérification existantes
        $verificationInfo = $this->emailVerificationService->getVerificationInfo($request->email);
        
        if (!$verificationInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune vérification en cours.'
            ]);
        }

        // Annuler la vérification actuelle
        $this->emailVerificationService->cancelVerification($request->email);

        // Envoyer un nouveau code (les données utilisateur ne sont plus disponibles, rediriger vers l'inscription)
        return response()->json([
            'success' => false,
            'message' => 'Session expirée. Veuillez recommencer l\'inscription.',
            'redirect' => route('register')
        ]);
    }

    /**
     * Validation des données d'inscription
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'country_code' => ['required', 'string', 'exists:countries,code'],
            'currency' => ['required', 'string', 'in:XOF,USD,EUR'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ], [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'country_code.required' => 'Veuillez sélectionner votre pays.',
            'currency.required' => 'Veuillez sélectionner votre devise.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'terms.required' => 'Vous devez accepter les conditions d\'utilisation.',
        ]);
    }
}
