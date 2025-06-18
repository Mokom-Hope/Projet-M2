<?php

namespace App\Http\Controllers\DashboardController;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Bien;
use App\Models\Reservation;
use App\Models\Notification;
use App\Models\Support;


class DashboardController extends Controller
{
  /**
   * Afficher le tableau de bord
   */
  public function index()
  {
      return view('dashboard.index');
  }

  /**
   * Afficher la liste des biens du propriétaire
   */
  public function properties()
  {
      return view('dashboard.properties');
  }

  /**
   * Afficher la liste des réservations
   */

//   public function reservations()
//   {
//       return view('dashboard.reservations');
//   }

  
  public function reservations()
{
    $biens = Bien::where('id_proprietaire', Auth::id())->pluck('id');
    $reservations = Reservation::whereIn('id_bien', $biens)
        ->with(['bien', 'client'])
        ->latest()
        ->get();

    return view('dashboard.reservations', compact('reservations'));
}


  /**
   * Afficher les détails d'une réservation
   */
  public function showReservation($id)
  {
      $reservation = Reservation::with(['bien', 'client'])->findOrFail($id);
      
      // Vérifier si l'utilisateur est le propriétaire du bien ou le client
      if (Auth::id() !== $reservation->bien->id_proprietaire && Auth::id() !== $reservation->id_client) {
          return redirect()->route('dashboard')->with('error', 'Vous n\'êtes pas autorisé à voir cette réservation.');
      }

      return view('dashboard.reservation-details', compact('reservation'));
  }

  /**
   * Afficher les messages
   */
  public function messages()
  {
      return view('dashboard.messages');
  }

  /**
   * Afficher le profil
   */
  public function profile()
  {
      return view('dashboard.profile');
  }

  /**
   * Mettre à jour le profil
   */
  public function updateProfile(Request $request)
  {
      $user = Auth::user();
      
      $validator = Validator::make($request->all(), [
          'nom' => 'required|string|max:255',
          'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
          'telephone' => 'required|string|max:20',
          'current_password' => 'nullable|required_with:password',
          'password' => 'nullable|string|min:8|confirmed',
      ]);
      
      if ($validator->fails()) {
          return redirect()->back()
              ->withErrors($validator)
              ->withInput($request->except('password', 'password_confirmation', 'current_password'));
      }
      
      // Vérifier le mot de passe actuel si un nouveau mot de passe est fourni
      if ($request->filled('password')) {
          if (!Hash::check($request->current_password, $user->password)) {
              return redirect()->back()
                  ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                  ->withInput($request->except('password', 'password_confirmation', 'current_password'));
          }
      }
      
      // Mettre à jour les informations de l'utilisateur
      $user->nom = $request->nom;
      $user->email = $request->email;
      $user->telephone = $request->telephone;
      
      if ($request->filled('password')) {
          $user->password = Hash::make($request->password);
      }
      
      $user->save();
      
      return redirect()->route('dashboard.profile')->with('success', 'Votre profil a été mis à jour avec succès.');
  }

  /**
   * API pour récupérer les statistiques du tableau de bord
   */
  public function apiGetStats()
  {
      $propertiesCount = Bien::where('id_proprietaire', Auth::id())->count();
      
      $properties = Bien::where('id_proprietaire', Auth::id())->pluck('id');
      $reservationsCount = Reservation::whereIn('id_bien', $properties)->count();
      
      $messagesCount = Notification::where('user_id', Auth::id())
          ->where('statut', 'NonLu')
          ->count();

      return response()->json([
          'properties_count' => $propertiesCount,
          'reservations_count' => $reservationsCount,
          'messages_count' => $messagesCount
      ]);
  }
}