<?php

namespace App\Services\Gateways;

interface PaymentGatewayInterface
{
    public function charge(array $data): array;
    
    public function transfer(array $data): array;
    
    public function getSupportedMethods(?string $country = null): array;
    
    public function verifyTransaction(string $reference): array;
}
