<?php

namespace Acelle\Library\Automation;

use Carbon\Carbon;

class Webhook extends Action
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
        usleep(100000); // (0.1 second) to avoid same day with previous action when modifying (n days)

        if (config('app.demo') == true) {
            $check = (bool) random_int(0, 1);
            return $check;
        }

        // CODE HERE
        $subscriber = $this->autoTrigger->subscriber;
        $httpConfig = \Acelle\Model\HttpConfig::findByUid($this->options['http_config_uid']);
        // Execute webhook
        $httpRequest = $httpConfig->run($subscriber->translateIncomingWebhookTags());

        // result
        if ($httpRequest->isSuccess()) {
            return true;
        } else {
            return false;
        }
    }

    public function getActionDescription()
    {
        return trans('messages.automation.action.http_request.description');
    }

    public function getProgressDescription($timezone = null, $locale = null)
    {
        if (is_null($this->getLastExecuted())) {
            return trans('messages.automation.action.http_request.description');
        } else {
            return trans('messages.automation.action.http_request.executed');
        }
    }

    public function isDelayAction()
    {
        return false;
    }
}
