@extends('layouts.app')

@section('title', 'ImmoChain - Mot de passe oublié')

@section('content')
<div class="container mx-auto px-4 py-16">
  <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-sm border">
      <h1 class="text-2xl font-bold mb-6 text-center">Réinitialisation du mot de passe</h1>
      
      @if (session('success'))
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          {{ session('success') }}
      </div>
      @endif
      
      @if ($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <ul class="list-disc pl-5">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
      @endif
      
      <div class="mb-6">
          <p class="text-gray-600">Veuillez saisir votre adresse email. Nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
      </div>
      
      <form action="{{ route('password.email') }}" method="POST">
          @csrf
          
          <div class="mb-6">
              <label for="email" class="block mb-1 font-medium">Email</label>
              <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full border rounded-lg px-3 py-2" required>
          </div>
          
          <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-medium hover:bg-gray-800 transition">
              Envoyer le lien de réinitialisation
          </button>
      </form>
      
      <div class="mt-6 text-center">
          <a href="{{ route('login') }}" class="text-gray-500 hover:underline">Retour à la connexion</a>
      </div>
  </div>
</div>
@endsection

