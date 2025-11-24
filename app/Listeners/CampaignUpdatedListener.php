<?php

namespace Acelle\Listeners;

use Acelle\Events\CampaignUpdated;
use Acelle\Jobs\UpdateCampaignJob;

class CampaignUpdatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CampaignUpdated  $event
     * @return void
     */
    public function handle(CampaignUpdated $event)
    {
        if ($event->delayed) {
            UpdateCampaignJob::dispatch($event->campaign);
        } else {
            UpdateCampaignJob::dispatchSync($event->campaign);
        }
    }
}
