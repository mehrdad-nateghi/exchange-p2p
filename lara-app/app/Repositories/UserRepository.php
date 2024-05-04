<?php

namespace App\Repositories;

use App\Enums\old\LinkedMethodStatusEnum;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get the active linked methods of an applicant
     */
    public function getActiveLinkedMethods(User $applicant)
    {
        return $applicant->linkedMethods()
            ->where('status', LinkedMethodStatusEnum::Active)
            ->get();
    }

    /**
     * Get the payment methods' ids which applicant linked them
     */
    public function getPaymentMethodsUserLinked(User $applicant)
    {
        $applicant_linked_methods = $this->getActiveLinkedMethods($applicant);

        return $applicant_linked_methods->map(function ($linkedMethod) {
            return $linkedMethod->paymentMethod->id;
        })->toArray();
    }

    /**
     * Get corresponding linked methods of an applicant based on input payment methods
     */
    public function getPeerLinkedMethods(User $applicant, array $paymentMethods)
    {
        $linked_methods = [];
        foreach($paymentMethods as $pm) {
            $lm = $applicant->linkedMethods()->where('method_type_id', $pm)->first();
            $linked_methods[] = $lm->id;
        }

        return $linked_methods;
    }


}
