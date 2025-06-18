@extends('layouts.app')

@section('title', 'Vérification Email')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Vérifiez votre email</h1>
                <p class="text-indigo-100">Nous avons envoyé un code à</p>
                <p class="text-white font-semibold">{{ $email }}</p>
            </div>

            <!-- Progress Steps -->
            <div class="px-8 py-4 bg-gray-50 border-b">
                <div class="flex items-center justify-center">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="ml-2 text-sm font-medium text-green-600">Informations</span>
                        </div>
                        <div class="w-8 h-px bg-indigo-300"></div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">2</div>
                            <span class="ml-2 text-sm font-medium text-indigo-600">Vérification</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-8">
                <!-- Timer -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center px-4 py-2 bg-orange-50 border border-orange-200 rounded-full">
                        <svg class="w-4 h-4 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-orange-800">
                            Code expire dans <span id="countdown" class="font-bold">10:00</span>
                        </span>
                    </div>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-3">
                                @foreach ($errors->all() as $error)
                                    <p class="text-sm text-red-700">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Success Messages -->
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Code Input Form -->
                <form action="{{ route('register.verify.code') }}" method="POST" id="verifyForm">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    
                    <div class="mb-6">
                        <label for="code" class="block text-sm font-semibold text-gray-700 mb-3 text-center">
                            Entrez le code à 6 chiffres
                        </label>
                        
                        <!-- Code Input Grid -->
                        <div class="flex justify-center space-x-3 mb-4">
                            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" data-index="0">
                            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" data-index="1">
                            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" data-index="2">
                            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" data-index="3">
                            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" data-index="4">
                            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" data-index="5">
                        </div>
                        
                        <!-- Hidden input for form submission -->
                        <input type="hidden" name="code" id="hiddenCode">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="verifyBtn" disabled class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transform transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Vérifier le code
                        </span>
                    </button>
                </form>

                <!-- Resend Code -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 mb-2">Vous n'avez pas reçu le code ?</p>
                    <button type="button" id="resendBtn" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm transition-colors duration-200">
                        Renvoyer le code
                    </button>
                </div>

                <!-- Help -->
                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Besoin d'aide ?</h3>
                            <p class="text-sm text-blue-700 mt-1">
                                Vérifiez votre dossier spam ou contactez notre support si vous ne recevez pas le code.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div class="text-center mt-6">
            <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium transition-colors duration-200">
                ← Retour à l'inscription
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInputs = document.querySelectorAll('.code-input');
    const hiddenCodeInput = document.getElementById('hiddenCode');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const form = document.getElementById('verifyForm');
    
    // Countdown timer
    let timeLeft = 600; // 10 minutes in seconds
    const countdownElement = document.getElementById('countdown');
    
    const timer = setInterval(() => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            countdownElement.textContent = 'Expiré';
            codeInputs.forEach(input => input.disabled = true);
            verifyBtn.disabled = true;
        }
        timeLeft--;
    }, 1000);
    
    // Code input handling
    codeInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }
            
            // Move to next input
            if (value && index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
            }
            
            updateHiddenInput();
            updateSubmitButton();
        });
        
        input.addEventListener('keydown', function(e) {
            // Handle backspace
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                codeInputs[index - 1].focus();
            }
            
            // Handle paste
            if (e.key === 'v' && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();
                navigator.clipboard.readText().then(text => {
                    const digits = text.replace(/\D/g, '').slice(0, 6);
                    digits.split('').forEach((digit, i) => {
                        if (codeInputs[i]) {
                            codeInputs[i].value = digit;
                        }
                    });
                    updateHiddenInput();
                    updateSubmitButton();
                    
                    // Auto-submit if complete
                    if (digits.length === 6) {
                        setTimeout(() => form.submit(), 500);
                    }
                });
            }
        });
        
        // Focus first input
        if (index === 0) {
            input.focus();
        }
    });
    
    function updateHiddenInput() {
        const code = Array.from(codeInputs).map(input => input.value).join('');
        hiddenCodeInput.value = code;
    }
    
    function updateSubmitButton() {
        const code = Array.from(codeInputs).map(input => input.value).join('');
        verifyBtn.disabled = code.length !== 6;
        
        // Auto-submit when complete
        if (code.length === 6) {
            setTimeout(() => {
                if (code === Array.from(codeInputs).map(input => input.value).join('')) {
                    form.submit();
                }
            }, 500);
        }
    }
    
    // Resend code
    resendBtn.addEventListener('click', function() {
        // Disable button temporarily
        this.disabled = true;
        this.textContent = 'Envoi en cours...';
        
        fetch('{{ route("register.resend") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                email: '{{ $email }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reset timer
                timeLeft = 600;
                codeInputs.forEach(input => {
                    input.disabled = false;
                    input.value = '';
                });
                verifyBtn.disabled = true;
                codeInputs[0].focus();
                
                // Show success message
                showToast('Code renvoyé avec succès !', 'success');
            } else {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    showToast(data.message, 'error');
                }
            }
        })
        .catch(error => {
            showToast('Erreur lors du renvoi du code', 'error');
        })
        .finally(() => {
            this.disabled = false;
            this.textContent = 'Renvoyer le code';
        });
    });
    
    function showToast(message, type) {
        // Simple toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-medium z-50 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
</script>
@endpush
@endsection
