<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class LoginController extends Controller
{
    protected $redirectTo = '/dashboard';

    public function __construct(
        private SecurityService $securityService
    ) {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Gérer la connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if ($user->isLocked()) {
            throw ValidationException::withMessages([
                'email' => [__('auth.locked')],
            ]);
        }

        if ($user->two_factor_enabled) {
            // Stocker les identifiants dans la session pour la vérification 2FA
            $request->session()->put('login.id', $user->id);
            return redirect()->route('2fa.verify');
        }

        $this->loginUser($user, $request);

        return redirect()->intended($this->redirectTo);
    }

    /**
     * Afficher le formulaire de vérification 2FA
     */
    public function showTwoFactorForm()
    {
        if (!session('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    /**
     * Vérifier le code 2FA
     */
    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'two_factor_code' => ['required', 'string'],
        ]);

        $userId = session('login.id');
        $user = User::findOrFail($userId);

        $google2fa = app(Google2FA::class);

        if ($google2fa->verifyKey($user->two_factor_secret, $request->two_factor_code)) {
            $this->loginUser($user, $request);
            $request->session()->forget('login.id');
            return redirect()->intended($this->redirectTo);
        }

        throw ValidationException::withMessages([
            'two_factor_code' => [__('auth.invalid_two_factor_code')],
        ]);
    }

    /**
     * Connecter l'utilisateur
     */
    protected function loginUser(User $user, Request $request)
    {
        Auth::login($user, $request->filled('remember'));

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        $this->securityService->logLogin($user, $request);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}