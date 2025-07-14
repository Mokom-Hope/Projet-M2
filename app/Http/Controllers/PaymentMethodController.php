<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Models\Country;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    private PaymentGatewayService $paymentService;

    public function __construct(PaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $paymentMethods = Auth::user()->paymentMethods()
            ->with('country')
            ->latest()
            ->get();

        $supportedMethods = $this->paymentService->getSupportedMethods();
        
        return view('payment-methods.index', compact('paymentMethods', 'supportedMethods'));
    }

    public function create()
    {
        $countries = Country::active()->get();
        $supportedMethods = $this->paymentService->getSupportedMethods();
        
        return view('payment-methods.create', compact('countries', 'supportedMethods'));
    }

    public function store(StorePaymentMethodRequest $request)
    {
        try {
            $paymentMethod = Auth::user()->paymentMethods()->create([
                'type' => $request->type,
                'provider' => $request->provider,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'country_code' => Country::find($request->country_id)->code,
                'metadata' => $request->metadata ?? [],
                'is_default' => $request->is_default ?? false,
                'status' => 'active'
            ]);

            return redirect()->route('payment-methods.index')
                ->with('success', 'Méthode de paiement ajoutée avec succès !');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()]);
        }
    }

    public function show(PaymentMethod $paymentMethod)
    {
        $this->authorize('view', $paymentMethod);
        
        return view('payment-methods.show', compact('paymentMethod'));
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);
        
        $countries = Country::active()->get();
        $supportedMethods = $this->paymentService->getSupportedMethods();
        
        return view('payment-methods.edit', compact('paymentMethod', 'countries', 'supportedMethods'));
    }

    public function update(StorePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);
        
        try {
            $paymentMethod->update([
                'account_name' => $request->account_name,
                'is_default' => $request->is_default ?? false,
                'metadata' => $request->metadata ?? [],
            ]);

            return redirect()->route('payment-methods.index')
                ->with('success', 'Méthode de paiement mise à jour !');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $this->authorize('delete', $paymentMethod);
        
        try {
            $paymentMethod->delete();
            
            return redirect()->route('payment-methods.index')
                ->with('success', 'Méthode de paiement supprimée !');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    public function setDefault(PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);
        
        try {
            // Désactiver toutes les autres méthodes par défaut de l'utilisateur
            Auth::user()->paymentMethods()->update(['is_default' => false]);
        
            // Activer celle-ci comme par défaut
            $paymentMethod->update(['is_default' => true]);
        
            return redirect()->route('payment-methods.index')
                ->with('success', 'Méthode de paiement définie par défaut !');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }
}
