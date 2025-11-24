<?php

namespace Acelle\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Acelle\Model\Campaign;

class QueueAdjust extends Command
{
    protected $signature = 'queue:adjust';
    protected $description = 'Launch up to 20 worker processes if priority campaigns exist';

    protected $pidFile = 'queue_pids.json';
    protected $maxProcesses = 20;
    protected $priorityQueueName = 'custom01';
    protected $command = "sleep 10";

    public function handle()
    {
        // Check for active campaigns from priority customers
        $hasRunningCampaign = Campaign::sending()->where(function ($q) {
            $q->sending();
        })
            ->orWhere(function ($q) {
                $q->queued();
            })->whereIn('customer_id', function ($query) {
                $query->select('id')
                    ->from('customers')
                    ->where('custom_queue_name', $this->priorityQueueName);
            })
            ->count();

        if (!$hasRunningCampaign) {
            $this->info("No active priority campaigns.");
            return Command::SUCCESS;
        }

        applog('queue')->info("There are {$hasRunningCampaign} priority campaigns running...");

        // Load current PIDs
        $pids = $this->loadPids();

        // Clean up dead ones
        $pids = $this->cleanupPids($pids);

        $running = count($pids);
        $this->info("Currently running: $running processes.");
        applog('queue')->info("Currently running: $running extra processes.");

        while ($running < $this->maxProcesses) {
            $pid = exec("nohup {$this->command} > /dev/null 2>&1 & echo $!");

            if ($pid) {
                $pids[] = [
                    'pid' => $pid,
                    'command' => $this->command,
                    'started_at' => now()->toDateTimeString()
                ];
                $this->info("Launched new worker PID: $pid => {$this->command}");
                applog('queue')->info("+ Launched new worker PID: $pid => {$this->command}");
                $running++;
            } else {
                $this->error("Failed to launch process");
                applog('queue')->error("Failed to launch process");
                break;
            }
        }

        $this->savePids($pids);

        return Command::SUCCESS;
    }

    protected function loadPids()
    {
        if (!Storage::exists($this->pidFile)) {
            return [];
        }
        return json_decode(Storage::get($this->pidFile), true) ?? [];
    }

    protected function savePids(array $pids)
    {
        Storage::put($this->pidFile, json_encode($pids, JSON_PRETTY_PRINT));
    }

    protected function cleanupPids(array $pids)
    {
        $alive = [];
        foreach ($pids as $entry) {
            $pid = $entry['pid'];
            if ($this->isAlive($pid)) {
                $alive[] = $entry;
            } else {
                $this->warn("PID $pid not alive. Removing.");
            }
        }
        return $alive;
    }

    protected function isAlive($pid)
    {
        if (function_exists('posix_kill')) {
            return posix_kill($pid, 0);
        }
        $result = shell_exec("ps -p $pid");
        return strpos($result, (string)$pid) !== false;
    }
}
