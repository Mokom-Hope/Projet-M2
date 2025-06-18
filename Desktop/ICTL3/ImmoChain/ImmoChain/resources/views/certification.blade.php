// Dans la vue de détail d'un bien
@if($property->blockchain_registered)
<div class="mb-6">
    <h2 class="text-xl font-semibold mb-2">Certification Blockchain</h2>
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-shield-alt text-green-500 text-2xl mr-3"></i>
            <div>
                <p class="font-medium text-green-800">Ce bien est certifié sur la blockchain</p>
                <p class="text-sm text-green-700 mt-1">
                    Les informations de ce bien immobilier sont enregistrées de manière sécurisée et immuable sur la blockchain.
                </p>
                <a href="{{ route('blockchain.verify.property', $property->id) }}" target="_blank" class="inline-flex items-center text-sm text-green-600 hover:text-green-800 mt-2">
                    <i class="fas fa-external-link-alt mr-1"></i> Vérifier sur la blockchain
                </a>
            </div>
        </div>
    </div>
</div>
@endif