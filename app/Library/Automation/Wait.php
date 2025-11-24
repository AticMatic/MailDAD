<?php

namespace Acelle\Library\Automation;

use Carbon\Carbon;

class Wait extends Action
{
    /*****

        Wait action may result in the following cases:
          + True - pass, go to next step
          + False - not pass, wait...
          + Exception - for any reason
        In case of Exception, it is better to stop the whole automation process and write error log to the automation
        so that the responsible person can check it

        Then, "last_executed" is used as a flag indicating that the process is done
        Return FALSE or TRUE (update last_executed before returning true)

    ****/

    protected function doExecute($manually)
    {
        if (config('app.demo') == true) {
            $check = (bool) random_int(0, 1);
            return $check;
        }

        list($isDue, $dueTime) = $this->checkDue();

        if ($isDue) {
            usleep(1000); // (0.001 second) to avoid same day with previous action when modifying (n days)
            $this->logger->info(sprintf('---> %s: already %s minutes (or %s hours) due! marking as done and moving to next action!', get_class($this), now()->diffInMinutes($dueTime), now()->diffInHours($dueTime)));
        } else {
            $this->logger->info(sprintf('---> %s: note due yet, wait for another %s minutes (or %s hours)...', get_class($this), now()->diffInMinutes($dueTime), now()->diffInHours($dueTime)));
        }

        return $isDue;
    }

    protected function checkDue()
    {
        // Wait for a specified time, counted from the parent's execution time

        $dueTime = $this->getDueTime();
        $isDue = now()->gte($dueTime);

        return [$isDue, $dueTime];
    }

    public function getActionDescription()
    {
        $nameOrEmail = $this->autoTrigger->getSubscriberCachedInfo('email', $fallback = true, $default = '[email]');

        return trans('messages.automation.action.wait.executed_description', [ 'wait' => $this->getOption('time') ]);
    }

    public function getProgressDescription($timezone = null, $locale = null)
    {
        if (is_null($this->getLastExecuted())) {
            list($isDue, $dueTime) = $this->checkDue();

            $timezone = $timezone ?? config('app.timezone');
            $locale = $locale ?? config('app.locale');
            $dueTime->timezone($timezone);
            $until = format_datetime($dueTime, 'datetime_short', $locale);

            if ($isDue) {
                // Something went wrong, it should've executed already
                return trans('messages.automation.action.wait.status_description_expired', [
                    'wait' => $this->getOption('time'),
                    'until' => $until,
                    'diff' => $dueTime->diffForHumans()
                ]);
            } else {
                return trans('messages.automation.action.wait.status_description', [
                    'wait' => $this->getOption('time'),
                    'until' => $until,
                    'diff' => $dueTime->diffForHumans()
                ]);
            }
        } else {
            return trans('messages.automation.action.wait.status_description_done', [
                'wait' => $this->getOption('time')
            ]);
        }
    }

    public function getDueTime()
    {
        $timezone = $timezone ?? config('app.timezone');

        $waitDuration = $this->getOption('time');  // 1 hour, 1 day, 2 days
        $parentExecutionTime = Carbon::createFromTimestamp($this->getParent()->getLastExecuted());
        $dueTime = $parentExecutionTime->modify($waitDuration);

        return $dueTime;
    }

    public function isDelayAction()
    {
        return true;
    }
}
