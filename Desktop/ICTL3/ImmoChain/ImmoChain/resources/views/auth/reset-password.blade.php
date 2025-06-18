@extends('layouts.app')

@section('title', 'ImmoChain - Réinitialisation du mot de passe')

@section('content')
<div class="container mx-auto px-4 py-16">
  <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-sm border">
      <h1 class="text-2xl font-bold mb-6 text-center">Réinitialisation du mot de passe</h1>
      
      @if ($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <ul class="list-disc pl-5">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
      @endif
      
      <form action="{{ route('password.update') }}" method="POST">
          @csrf
          
          <input type="hidden" name="token" value="{{ $token }}">
          <input type="hidden" name="email" value="{{ $email }}">
          
          <div class="mb-4">
              <label for="password" class="block mb-1 font-medium">Nouveau mot de passe</label>
              <input type="password" id="password" name="password" class="w-full border rounded-lg px-3 py-2" required>
          </div>
          
          <div class="mb-6">
              <label for="password_confirmation" class="block mb-1 font-medium">Confirmer le mot de passe</label>
              <input type="password" id="password_confirmation" name="password_confirmation" class="w-full border rounded-lg px-3 py-2" required>
          </div>
          
          <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-medium hover:bg-gray-800 transition">
              Réinitialiser le mot de passe
          </button>
      </form>
      
      <div class="mt-6 text-center">
          <a href="{{ route('login') }}" class="text-gray-500 hover:underline">Retour à la connexion</a>
      </div>
  </div>
</div>
@endsection

