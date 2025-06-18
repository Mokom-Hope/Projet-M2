<?php

namespace App\Http\Controllers\AuthController;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorAuthMail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials, false)) {
            // Générer un code de vérification à 6 chiffres
            $code = mt_rand(100000, 999999);
            $user = Auth::user();
            
            // Stocker le code et sa date d'expiration (10 minutes)
            $user->two_factor_code = $code;
            $user->two_factor_expires_at = Carbon::now()->addMinutes(10);
            $user->save();
            
            // Envoyer le code par email
            Mail::to($user->email)->send(new TwoFactorAuthMail($code));
            
            // Déconnecter l'utilisateur pour qu'il vérifie son code
            Auth::logout();
            
            // Au lieu d'utiliser la session, nous allons encoder l'email dans un token temporaire
            $tempToken = base64_encode(json_encode([
                'email' => $request->email,
                'remember' => $request->has('remember'),
                'expires' => Carbon::now()->addMinutes(10)->timestamp
            ]));
            
            // Rediriger vers la page de vérification avec le token dans l'URL
            return redirect()->route('verify.show', ['token' => $tempToken]);
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->withInput($request->except('password'));
    }

    /**
     * Afficher le formulaire de vérification 2FA
     */
    public function showVerificationForm(Request $request)
    {
        $token = $request->token;
        
        if (!$token) {
            Log::warning('Token non trouvé, redirection vers login');
            return redirect()->route('login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }
        
        try {
            $data = json_decode(base64_decode($token), true);
            
            // Vérifier si le token a expiré
            if (Carbon::createFromTimestamp($data['expires'])->isPast()) {
                Log::warning('Token expiré, redirection vers login');
                return redirect()->route('login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
            }
            
            $email = $data['email'];
            
            if (!$email) {
                Log::warning('Email non trouvé dans le token, redirection vers login');
                return redirect()->route('login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
            }
            
            // Vérifier si l'utilisateur existe
            $user = User::where('email', $email)->first();
            if (!$user) {
                Log::warning('Utilisateur non trouvé, redirection vers login');
                return redirect()->route('login')->with('error', 'Utilisateur non trouvé. Veuillez vous reconnecter.');
            }
            
            return view('auth.verify', ['token' => $token, 'email' => $email]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du décodage du token: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Une erreur est survenue. Veuillez vous reconnecter.');
        }
    }

    /**
     * Vérifier le code 2FA
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6',
            'token' => 'required'
        ]);
        
        try {
            $data = json_decode(base64_decode($request->token), true);
            
            // Vérifier si le token a expiré
            if (Carbon::createFromTimestamp($data['expires'])->isPast()) {
                return redirect()->route('login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
            }
            
            $email = $data['email'];
            $remember = $data['remember'] ?? false;
            
            if (!$email) {
                return redirect()->route('login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
            }
            
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Utilisateur non trouvé. Veuillez vous reconnecter.');
            }
            
            // Vérifier si le code est correct et non expiré
            if ($user->two_factor_code != $request->code) {
                return back()->withErrors(['code' => 'Le code de vérification est incorrect.'])->with('token', $request->token);
            }
            
            if (Carbon::now()->isAfter($user->two_factor_expires_at)) {
                return back()->withErrors(['code' => 'Le code de vérification a expiré. Veuillez vous reconnecter.']);
            }
            
            // Réinitialiser le code
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
            $user->save();
            
            // Connecter l'utilisateur
            Auth::login($user, $remember);
            
            // Régénérer la session
            $request->session()->regenerate();
            
            return redirect()->intended('/')->with('success', 'Connexion réussie! Bienvenue ' . $user->nom);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Une erreur est survenue. Veuillez vous reconnecter.');
        }
    }

    /**
     * Renvoyer le code de vérification
     */
    public function resendCode(Request $request)
    {
        $token = $request->token;
        
        if (!$token) {
            return redirect()->route('login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }
        
        try {
            $data = json_decode(base64_decode($token), true);
            $email = $data['email'];
            
            if (!$email) {
                return redirect()->route('login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
            }
            
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Utilisateur non trouvé. Veuillez vous reconnecter.');
            }
            
            // Générer un nouveau code
            $code = mt_rand(100000, 999999);
            
            // Mettre à jour le code et sa date d'expiration
            $user->two_factor_code = $code;
            $user->two_factor_expires_at = Carbon::now()->addMinutes(10);
            $user->save();
            
            // Envoyer le code par email
            Mail::to($user->email)->send(new TwoFactorAuthMail($code));
            
            return back()->with('success', 'Un nouveau code de vérification a été envoyé à votre adresse email.')->with('token', $token);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du renvoi du code: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Une erreur est survenue. Veuillez vous reconnecter.');
        }
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Traiter l'inscription
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'required|string|max:20',
            'type_utilisateur' => 'required|in:Client,Propriétaire',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Déboguer la valeur reçue
        Log::info('Type utilisateur reçu: ' . $request->type_utilisateur);

        $user = User::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
            'type_utilisateur' => $request->type_utilisateur,
            'remember_token' => Str::random(10), // Ajouter un remember_token par défaut
        ]);

        // Rediriger vers la page de connexion au lieu de connecter automatiquement
        return redirect()->route('login')->with('success', 'Inscription réussie ! Veuillez vous connecter avec vos identifiants.');
    }

    /**
     * Afficher le formulaire de demande de réinitialisation de mot de passe
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Traiter la demande de réinitialisation de mot de passe
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Aucun compte n\'est associé à cette adresse email.',
        ]);

        // Supprimer les anciens tokens pour cet email
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Créer un nouveau token
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addHours(1);

        // Stocker le token dans la base de données
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
            'expires_at' => $expiresAt,
        ]);

        // Envoyer l'email avec le lien de réinitialisation
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
        Mail::to($request->email)->send(new PasswordResetMail($resetUrl, $expiresAt));

        return back()->with('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
    }

    /**
     * Afficher le formulaire de réinitialisation de mot de passe
     */
    public function showResetPasswordForm($token, Request $request)
    {
        $email = $request->email;
        
        // Vérifier si le token existe et n'est pas expiré
        $reset = DB::table('password_resets')
            ->where('email', $email)
            ->first();
            
        if (!$reset || Carbon::now()->isAfter(Carbon::parse($reset->expires_at))) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Le lien de réinitialisation est invalide ou a expiré.']);
        }
        
        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Vérifier si le token existe et n'est pas expiré
        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();
            
        if (!$reset) {
            return back()->withErrors(['email' => 'Le lien de réinitialisation est invalide.']);
        }
        
        if (Carbon::now()->isAfter(Carbon::parse($reset->expires_at))) {
            return back()->withErrors(['email' => 'Le lien de réinitialisation a expiré.']);
        }
        
        // Vérifier le token
        if (!Hash::check($request->token, $reset->token)) {
            return back()->withErrors(['email' => 'Le lien de réinitialisation est invalide.']);
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Supprimer le token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
