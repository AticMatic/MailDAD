<?php

namespace Acelle\Library\Contracts;

interface PlanInterface
{
    public function isFree();
    public function isActive();
    public function getPrice();
    public function hasTrial();
    public function getFrequencyAmount();
    public function getFrequencyUnit();
    public function getTrialAmount();
    public function getTrialUnit();

    // handle when subscription activated
    public function handleSubscriptionActivatedSuccess(\Acelle\Model\Subscription $subscription);

    // handle when subscription renewed
    public function handleSubscriptionRenewedSuccess(\Acelle\Model\Subscription $subscription);

    // handle when plan changed
    public function handlePlanChangedSuccess(\Acelle\Model\Subscription $subscription);
}
