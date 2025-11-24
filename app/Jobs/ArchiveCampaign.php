<?php

namespace Acelle\Jobs;

class ArchiveCampaign extends Base
{

    public $timeout = 7200;

    protected $campaign;

    /**
     * Create a new job instance.
     */
    public function __construct($campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->campaign->logger()->info('Archiving...');
            $this->campaign->archive();
            $this->campaign->logger()->info('Archiving done!');
        } catch (\Throwable $ex) {
            $errorMsg = "Archiving failed: ".$ex->getMessage()."\n".$ex->getTraceAsString();

            $this->campaign->logger()->info($errorMsg);
            $this->campaign->setArchiveStatusError($errorMsg);
        }
    }
}
