<?php

namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->id === $paymentMethod->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->id === $paymentMethod->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->id === $paymentMethod->user_id;
    }
}
