<?php

namespace Acelle\Policies;

use Acelle\Model\User;
use Acelle\Model\Webhook;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebhookPolicy
{
    use HandlesAuthorization;

    public function list(User $user)
    {
        return true;
    }

    public function read(User $user, Webhook $webhook)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Webhook $webhook)
    {
        return true;
    }

    public function enable(User $user, Webhook $webhook)
    {
        return $webhook->isInactive() && $webhook->httpConfig;
        ;
    }

    public function disable(User $user, Webhook $webhook)
    {
        return $webhook->isActive();
    }

    public function delete(User $user, Webhook $webhook)
    {
        return true;
    }

    public function verify(User $user, Webhook $webhook)
    {
        return true;
    }

    public function test(User $user, Webhook $webhook)
    {
        return $webhook->httpConfig;
    }

    public function viewLog(User $user, Webhook $webhook)
    {
        return $webhook->httpConfig;
    }
}
