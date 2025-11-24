<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;
use Braintree\Http;

class Webhook extends Model
{
    use HasFactory;
    use HasUid;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public function httpConfig()
    {
        return $this->belongsTo(HttpConfig::class, 'http_config_id');
    }

    public static function newDefault()
    {
        $webhook = new static();
        $webhook->status = static::STATUS_INACTIVE;

        $webhook->setting_retry_times = 2;
        $webhook->setting_retry_after_seconds = 900;

        return $webhook;
    }

    public static function scopeSearch($query, $keyword)
    {
        // Keyword
        if (!empty(trim($keyword))) {
            foreach (explode(' ', trim($keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('webhooks.name', 'like', '%'.$keyword.'%');
                });
            }
        }
    }

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isInactive()
    {
        return $this->status == self::STATUS_INACTIVE;
    }

    public function disable()
    {
        $this->status = self::STATUS_INACTIVE;

        return $this->save();
    }

    public function enable()
    {
        $this->status = self::STATUS_ACTIVE;

        return $this->save();
    }

    public static function scopeActive($query)
    {
        $query->where('status', static::STATUS_ACTIVE);
    }

    public function saveWebhook($name, $event)
    {
        // fill
        $this->name = $name;
        $this->event = $event;

        $validator = \Validator::make($this->getAttributes(), [
            'name' => 'required',
            'event' => 'required',
        ]);

        if ($validator->fails()) {
            return [false, $validator->errors()];
        }

        // save default http config
        $httpConfig = $this->newDefaultHttpConfig();
        $httpConfig->save();
        // set http config id
        $this->http_config_id = $httpConfig->id;

        // Save the webhook
        $this->save();



        return [true, $validator->errors()];
    }

    public function getTags()
    {
        return array_map(function ($param) {
            return [
                'tag' => "{".$param."}",
                'label' => trans('messages.webhook.tag.' .$param),
            ];
        }, config('webhook_events')[$this->event]['params']);
    }

    public function test($requestDetails = []): HttpRequest
    {
        switch ($this->event) {
            case 'cancel_subscription':
                $params = [
                    'customer_id' => '{customer_test_id}',
                    'plan_id' => '{plan_test_id}',
                ];
                break;
            case 'new_subscription':
                $params = [
                    'customer_id' => '{customer_test_id}',
                    'plan_id' => '{plan_test_id}',
                ];
                break;
            case 'new_customer':
                $params = [
                    'customer_id' => '{customer_test_id}',
                ];
                break;
            case 'change_plan':
                $params = [
                    'customer_id' => '{customer_test_id}',
                    'old_plan_id' => '{old_plan_test_id}',
                    'new_plan_id' => '{new_plan_test_id}',
                ];
                break;
            case 'terminate_subscription':
                $params = [
                    'customer_id' => '{customer_test_id}',
                    'plan_id' => '{plan_test_id}',
                ];
                break;
            default:
                throw new \Exception('Unknown event: ' . $this->event);
        }

        // Dispatch the job with the given parameters
        return $this->httpConfig->test($params, $requestDetails);
    }

    public function run($params = [])
    {
        $httpRequest = $this->httpConfig->dispatch($params);
        $httpRequest->run();

        return $httpRequest;
    }

    public static function scopeBackend($query)
    {
        $query->whereNull('customer_id');
    }

    public function newDefaultHttpConfig()
    {
        $httpConfig = HttpConfig::newDefault();
        $httpConfig->customer_id = $this->customer_id;

        return $httpConfig;
    }

    public function getHttpConfig(): HttpConfig
    {
        if ($this->httpConfig) {
            return $this->httpConfig;
        } else {
            return $this->newDefaultHttpConfig();
        }
    }

    public function saveHttpConfig($attributes)
    {
        $httpConfig = $this->getHttpConfig();

        $httpConfig->fillAttributes($attributes);
        $httpConfig->save();

        // set id
        $this->http_config_id = $httpConfig->id;
        $this->save();
    }
}
