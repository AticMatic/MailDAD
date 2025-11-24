<?php

namespace Acelle\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Cache;

class UpdateCampaignJob extends Base implements ShouldBeUnique
{
    protected $campaign;
    protected $customer;

    public function __construct($campaign)
    {
        $this->campaign = $campaign;
        $this->customer = $this->campaign->customer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->customer->setUserDbConnection();
        if ($this->campaign->isArchived()) {
            $this->campaign->logger()->info('Campaign already archived');
        } else {
            $this->campaign->logger()->info("Caching");
            $start = now();
            $this->campaign->updateCache();
            $end = now();
            $this->campaign->logger()->info("Cache done, it took {$end->diffForHumans($start, true)}");
        }

    }

    public $uniqueFor = 1200; // 20 minutes
    public function uniqueId()
    {
        return $this->campaign->id;
    }

    public function uniqueVia()
    {
        return Cache::driver('file');
    }
}
