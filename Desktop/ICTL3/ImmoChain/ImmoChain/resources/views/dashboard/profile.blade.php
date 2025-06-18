@extends('layouts.app')

@section('title', 'ImmoChain - Mon profil')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Mon profil</h1>
        
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-gray-400 text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">{{ Auth::user()->nom }}</h2>
                        <p class="text-gray-500">{{ Auth::user()->type_utilisateur }}</p>
                    </div>
                </div>
                
                @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
                @endif
                
                <form action="{{ route('dashboard.profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="nom" class="block mb-1 font-medium">Nom complet</label>
                        <input type="text" id="nom" name="nom" value="{{ Auth::user()->nom }}" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div>
                        <label for="email" class="block mb-1 font-medium">Email</label>
                        <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div>
                        <label for="telephone" class="block mb-1 font-medium">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" value="{{ Auth::user()->telephone }}" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    
                    <div class="pt-4 border-t">
                        <h3 class="font-semibold mb-2">Changer le mot de passe</h3>
                        <p class="text-sm text-gray-500 mb-4">Laissez vide si vous ne souhaitez pas changer votre mot de passe</p>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block mb-1 font-medium">Mot de passe actuel</label>
                                <input type="password" id="current_password" name="current_password" class="w-full border rounded-lg px-3 py-2">
                            </div>
                            
                            <div>
                                <label for="password" class="block mb-1 font-medium">Nouveau mot de passe</label>
                                <input type="password" id="password" name="password" class="w-full border rounded-lg px-3 py-2">
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block mb-1 font-medium">Confirmer le nouveau mot de passe</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full border rounded-lg px-3 py-2">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

