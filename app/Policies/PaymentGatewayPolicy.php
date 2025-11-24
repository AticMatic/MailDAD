<?php

namespace Acelle\Policies;

use Acelle\Model\User;
use Acelle\Model\PaymentGateway;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentGatewayPolicy
{
    use HandlesAuthorization;

    public function list(User $user)
    {
        return true;
    }

    public function read(User $user, PaymentGateway $paymentGateway)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, PaymentGateway $paymentGateway)
    {
        return true;
    }

    public function enable(User $user, PaymentGateway $paymentGateway)
    {
        return $paymentGateway->isInactive();
    }

    public function disable(User $user, PaymentGateway $paymentGateway)
    {
        return $paymentGateway->isActive();
    }

    public function delete(User $user, PaymentGateway $paymentGateway)
    {
        return true;
    }

    public function verify(User $user, PaymentGateway $paymentGateway)
    {
        return true;
    }
}
