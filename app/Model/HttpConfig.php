<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;
use Braintree\Http;

class HttpConfig extends Model
{
    use HasFactory;
    use HasUid;

    public const REQUEST_METHOD_GET = 'get';
    public const REQUEST_METHOD_POST = 'post';
    public const REQUEST_METHOD_PUT = 'put';
    public const REQUEST_METHOD_DELETE = 'delete';

    public const REQUEST_AUTH_TYPE_BASIC_AUTH = 'basic_auth';
    public const REQUEST_AUTH_TYPE_BEARER_TOKEN = 'bearer_token';
    public const REQUEST_AUTH_TYPE_CUSTOM = 'custom';
    public const REQUEST_AUTH_TYPE_NO_AUTH = 'no_auth';

    public const REQUEST_BODY_TYPE_KEY_VALUE = 'key_value';
    public const REQUEST_BODY_TYPE_PLAIN = 'plain';

    public function httpRequests()
    {
        return $this->hasMany(HttpRequest::class, 'http_config_id');
    }

    public function fillAttributes($attributes)
    {
        if ($attributes['header_type'] == 'no_headers') {
            $attributes['request_headers'] = [];
        }

        if ($attributes['request_body_type'] == static::REQUEST_BODY_TYPE_PLAIN) {
            $attributes['request_body_params'] = [];
        } else {
            $attributes['request_body_plain'] = null;
        }

        // $this->name = $attributes['name'] ?? $this->name;
        // $this->setting_retry_times = $attributes['setting_retry_times'] ?? 0;
        // $this->setting_retry_after_seconds = $attributes['setting_retry_after_seconds'] ?? 900;
        $this->request_method = $attributes['request_method'];
        $this->request_url = $attributes['request_url'];
        $this->request_auth_type = $attributes['request_auth_type'];
        $this->request_auth_bearer_token = $attributes['request_auth_bearer_token'];
        $this->request_auth_basic_username = $attributes['request_auth_basic_username'];
        $this->request_auth_basic_password = $attributes['request_auth_basic_password'];
        $this->request_auth_custom_key = $attributes['request_auth_custom_key'];
        $this->request_auth_custom_value = $attributes['request_auth_custom_value'];
        $this->request_headers = json_encode($attributes['request_headers']);
        $this->request_body_type = $attributes['request_body_type'];
        $this->request_body_params = isset($attributes['request_body_params']) ? json_encode($attributes['request_body_params']) : json_encode([]);
        $this->request_body_plain = $attributes['request_body_plain'];
    }

    public static function newDefault()
    {
        $httpConfig = new static();

        // $httpConfig->setting_retry_times = 2;
        // $httpConfig->setting_retry_after_seconds = 900;
        $httpConfig->request_method = static::REQUEST_METHOD_GET;
        $httpConfig->request_auth_type = static::REQUEST_AUTH_TYPE_BASIC_AUTH;
        $httpConfig->request_body_type = static::REQUEST_BODY_TYPE_KEY_VALUE;

        return $httpConfig;
    }

    public function saveHttpConfig()
    {
        $validator = \Validator::make($this->getAttributes(), [
        ]);

        if ($validator->fails()) {
            return [false, $validator->errors()];
        }

        $this->save();

        return [true, $validator->errors()];
    }

    public function getRequestHeaders()
    {
        if (!$this->request_headers) {
            return [];
        }

        return json_decode($this->request_headers, true);
    }

    public function getRequestBodyParams()
    {
        if (!$this->request_body_params) {
            return [];
        }

        return json_decode($this->request_body_params, true);
    }

    public function makeRequest(array $params): HttpRequest
    {
        $httpRequest = $this->httpRequests()->make();

        $httpRequest->customer_id = $this->customer_id;
        $httpRequest->fillParams($params);
        $httpRequest->status = HttpRequest::STATUS_NEW;

        $httpRequest->save();

        return $httpRequest;
    }

    public function run($params = []): HttpRequest
    {
        $httpRequest = $this->makeRequest($params);
        $httpRequest->run();

        return $httpRequest;
    }

    public function test($params = [], $requestDetails = [])
    {
        $httpRequest = $this->makeRequest($params);

        if (!empty($requestDetails)) {
            $httpRequest->httpConfig->fillAttributes($requestDetails);
        }

        $httpRequest->run($params);

        return $httpRequest;
    }
}
