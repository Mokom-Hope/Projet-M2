<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load(['country', 'wallet', 'securityLogs' => function($query) {
            $query->latest()->limit(5);
        }]);
        
        $countries = Country::active()->get();
        
        return view('profile.show', compact('user', 'countries'));
    }

    public function edit()
    {
        $user = Auth::user();
        $countries = Country::active()->get();
        
        return view('profile.edit', compact('user', 'countries'));
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            // Gérer l'upload de photo
            if ($request->hasFile('profile_photo')) {
                // Supprimer l'ancienne photo
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                
                $data['profile_photo'] = $request->file('profile_photo')
                    ->store('profile-photos', 'public');
            }

            $user->update($data);

            return redirect()->route('profile.show')
                ->with('success', 'Profil mis à jour avec succès !');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = Auth::user();

            // Vérifier le mot de passe actuel
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect']);
            }

            // Mettre à jour le mot de passe
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return redirect()->route('profile.show')
                ->with('success', 'Mot de passe changé avec succès !');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors du changement: ' . $e->getMessage()]);
        }
    }

    public function toggleTwoFactor(Request $request)
    {
        try {
            $user = Auth::user();
            
            $user->update([
                'two_factor_enabled' => !$user->two_factor_enabled
            ]);

            $message = $user->two_factor_enabled 
                ? 'Authentification à deux facteurs activée !' 
                : 'Authentification à deux facteurs désactivée !';

            return redirect()->route('profile.show')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la modification: ' . $e->getMessage()]);
        }
    }
}
