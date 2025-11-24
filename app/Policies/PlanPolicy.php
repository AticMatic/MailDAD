<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Acelle\Model\User;
use Acelle\Model\PlanGeneral;

class PlanPolicy
{
    use HandlesAuthorization;

    public function read(User $user)
    {
        $can = $user->admin->getPermission('plan_read') != 'no';

        return $can;
    }

    public function readAll(User $user, PlanGeneral $plan)
    {
        $can = $user->admin->getPermission('plan_read') == 'all';

        return $can;
    }

    public function create(User $user)
    {
        $can = $user->admin->getPermission('plan_create') == 'yes';

        // config/limit.php
        $limit = app_profile('plan.limit');
        if (!is_null($limit)) {
            $planCount = PlanGeneral::count();
            $can = $can && ($planCount < $limit);
        } else {
            // ignore limit because it is null
        }

        return $can;
    }

    public function update(User $user, PlanGeneral $plan)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $plan->admin_id);

        return $can;
    }

    public function delete(User $user, PlanGeneral $plan)
    {
        $ability = $user->admin->getPermission('plan_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $plan->admin_id);

        return $can;
    }

    public function disable(User $user, PlanGeneral $plan)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $plan->admin_id);

        return $can && $plan->status != 'inactive';
    }

    public function enable(User $user, PlanGeneral $plan)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $plan->admin_id);

        return $can && $plan->status != 'active' && $plan->isValid();
    }

    public function visibleOn(User $user, PlanGeneral $plan)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $plan->admin_id);

        return $can && !$plan->visible && $plan->isActive();
    }

    public function visibleOff(User $user, PlanGeneral $plan)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $plan->admin_id);

        return $can && $plan->visible;
    }

    public function copy(User $user, PlanGeneral $plan)
    {
        $ability = $user->admin->getPermission('plan_copy');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $plan->admin_id);

        return $can;
    }
}
