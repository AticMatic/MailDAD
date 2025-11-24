<?php

namespace Acelle\Observers;

use Acelle\TrackingDomain;

class TrackingDomainObserver
{
    /**
     * Handle the TrackingDomain "created" event.
     */
    public function created(TrackingDomain $trackingDomain): void
    {
        //
    }

    /**
     * Handle the TrackingDomain "updated" event.
     */
    public function updated(TrackingDomain $trackingDomain): void
    {
        //
    }

    /**
     * Handle the TrackingDomain "deleted" event.
     */
    public function deleted(TrackingDomain $trackingDomain): void
    {
        //
    }

    /**
     * Handle the TrackingDomain "restored" event.
     */
    public function restored(TrackingDomain $trackingDomain): void
    {
        //
    }

    /**
     * Handle the TrackingDomain "force deleted" event.
     */
    public function forceDeleted(TrackingDomain $trackingDomain): void
    {
        //
    }
}
