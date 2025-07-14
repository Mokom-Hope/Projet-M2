<?php

namespace App\Providers;

use App\Models\PaymentMethod;
use App\Policies\PaymentMethodPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        PaymentMethod::class => PaymentMethodPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
