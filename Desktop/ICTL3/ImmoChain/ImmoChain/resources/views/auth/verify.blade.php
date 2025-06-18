@extends('layouts.app')

@section('title', 'ImmoChain - Vérification')

@section('content')
<div class="container mx-auto px-4 py-16">
  <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-sm border">
      <h1 class="text-2xl font-bold mb-6 text-center">Vérification à deux facteurs</h1>
      
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
          <p class="text-gray-600">Un code de vérification a été envoyé à votre adresse email <strong>{{ $email }}</strong>.</p>
          <p class="text-gray-600 mt-2">Veuillez saisir ce code pour finaliser votre connexion.</p>
      </div>
      
      <form action="{{ route('verify.post') }}" method="POST">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">
          
          <div class="mb-6">
              <label for="code" class="block mb-1 font-medium">Code de vérification</label>
              <input type="text" id="code" name="code" class="w-full border rounded-lg px-3 py-2 text-center text-2xl tracking-widest" maxlength="6" required autofocus>
              <p class="text-sm text-gray-500 mt-1">Le code est valide pendant 10 minutes.</p>
          </div>
          
          <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-medium hover:bg-gray-800 transition">
              Vérifier
          </button>
      </form>
      
      <div class="mt-6 text-center">
          <p>Vous n'avez pas reçu le code ? 
              <a href="{{ route('verify.resend', ['token' => $token]) }}" class="text-black font-medium hover:underline">Renvoyer le code</a>
          </p>
      </div>
      
      <div class="mt-6 text-center">
          <a href="{{ route('login') }}" class="text-gray-500 hover:underline">Retour à la connexion</a>
      </div>
  </div>
</div>
@endsection
