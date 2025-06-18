@extends('layouts.app')

@section('title', 'ImmoChain - Inscription')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-sm border">
        <h1 class="text-2xl font-bold mb-6 text-center">Inscription</h1>
        
        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="nom" class="block mb-1 font-medium">Nom complet</label>
                <input type="text" id="nom" name="nom" value="{{ old('nom') }}" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label for="email" class="block mb-1 font-medium">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label for="telephone" class="block mb-1 font-medium">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label for="type_utilisateur" class="block mb-1 font-medium">Type de compte</label>
                <select id="type_utilisateur" name="type_utilisateur" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="Client" {{ old('type_utilisateur') == 'Client' ? 'selected' : '' }}>Client</option>
                    <option value="Propriétaire" {{ old('type_utilisateur') == 'Propriétaire' ? 'selected' : '' }}>Propriétaire</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="password" class="block mb-1 font-medium">Mot de passe</label>
                <input type="password" id="password" name="password" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            
            <div class="mb-6">
                <label for="password_confirmation" class="block mb-1 font-medium">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            
            <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-medium hover:bg-gray-800 transition">
                S'inscrire
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p>Vous avez déjà un compte ? <a href="{{ route('login') }}" class="text-black font-medium hover:underline">Se connecter</a></p>
        </div>
    </div>
</div>
@endsection

