<?php

namespace Acelle\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MoveToBlacklist implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    use InteractsWithQueue;
    use Dispatchable;

    protected $subscribers;

    /**
     * Create a new job instance.
     */
    public function __construct($subscribers)
    {
        $this->subscribers = $subscribers;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Move selected subscribers to blacklist
        foreach ($this->subscribers as $subscriber) {
            $customer = $subscriber->mailList->customer;
            $subscriber->sendToUserBlacklist('Manually by user', $customer->id);

            // Log
            $subscriber->log('movedToBlacklist', $customer);

            // Timeline record
            \Acelle\Model\Timeline::recordCMovedToBlacklistByCustomer($subscriber, $customer);
        }
    }
}
