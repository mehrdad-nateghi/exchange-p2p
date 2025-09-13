<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getActiveLinkedMethods(User $user);
    public function getPaymentMethodsUserLinked(User $user);
    public function getPeerLinkedMethods(User $user, array $paymentMethods);

}
