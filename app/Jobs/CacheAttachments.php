<?php

namespace Acelle\Jobs;

use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CacheAttachments extends Base implements ShouldBeUnique
{
    public $uniqueFor = 3600; // seconds

    // @important: this job only works in Master Server
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
        if (config('custom.distributed_mode')) {
            if (!config('custom.distributed_master')) {
                $msg = 'CacheAttachments job must be executed on a Master server only (with DISTRIBUTED_MASTER=true in .env)';
                $this->campaign->setFailed($msg);
                throw new \Exception($msg);
            }
        }

        foreach ($this->campaign->attachments as $a) {
            $a->cacheToRedis();
        }
    }

    public function uniqueId(): string
    {
        return $this->campaign->uid;
    }
}
