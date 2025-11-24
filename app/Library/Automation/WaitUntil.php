<?php

namespace Acelle\Library\Automation;

use Carbon\Carbon;

class WaitUntil extends Wait
{
    public function getDueTime()
    {
        $until = $this->getOption('wait_until');  // wait until a speficific date/time
        $dueTime = new Carbon($until);

        return $dueTime;
    }

    public function getProgressDescription($timezone = null, $locale = null)
    {
        list($isDue, $dueTime) = $this->checkDue();

        $timezone = $timezone ?? config('app.timezone');
        $locale = $locale ?? config('app.locale');
        $dueTime->timezone($timezone);
        $until = format_datetime($dueTime, 'datetime_short', $locale);

        if (is_null($this->getLastExecuted())) {


            if ($isDue) {
                // Something went wrong, it should've executed already
                return trans('messages.automation.action.wait_until.status_description_expired', [
                    'until' => $until,
                    'diff' => $dueTime->diffForHumans()
                ]);
            } else {
                return trans('messages.automation.action.wait_until.status_description', [
                    'until' => $until,
                    'diff' => $dueTime->diffForHumans()
                ]);
            }
        } else {
            return trans('messages.automation.action.wait_until.status_description_done', [
                'until' => $until
            ]);
        }
    }
}
