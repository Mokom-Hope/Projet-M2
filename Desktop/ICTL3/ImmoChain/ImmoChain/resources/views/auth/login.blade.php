@extends('layouts.app')

@section('title', 'ImmoChain - Connexion')

@section('content')
<div class="container mx-auto px-4 py-16">
  <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-sm border">
      <h1 class="text-2xl font-bold mb-6 text-center">Connexion</h1>
      
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
      
      <form action="{{ route('login') }}" method="POST">
          @csrf
          
          <div class="mb-4">
              <label for="email" class="block mb-1 font-medium">Email</label>
              <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full border rounded-lg px-3 py-2" required>
          </div>
          
          <div class="mb-6">
              <label for="password" class="block mb-1 font-medium">Mot de passe</label>
              <input type="password" id="password" name="password" class="w-full border rounded-lg px-3 py-2" required>
          </div>
          
          <div class="flex items-center justify-between mb-6">
              <label class="flex items-center">
                  <input type="checkbox" name="remember" class="mr-2">
                  <span>Se souvenir de moi</span>
              </label>
              
              <a href="{{ route('password.request') }}" class="text-black hover:underline">Mot de passe oublié ?</a>
          </div>
          
          <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-medium hover:bg-gray-800 transition">
              Se connecter
          </button>
      </form>
      
      <div class="mt-6 text-center">
          <p>Vous n'avez pas de compte ? <a href="{{ route('register') }}" class="text-black font-medium hover:underline">S'inscrire</a></p>
      </div>
  </div>
</div>
@endsection

