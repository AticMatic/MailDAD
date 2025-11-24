<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Acelle\Model\User;
use Acelle\Model\PaymentMethod;

class PaymentMethodPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, PaymentMethod $paymentMethod)
    {
        return $paymentMethod->customer_id == $user->customer->id;
    }
}
