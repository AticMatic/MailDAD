<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;

class HttpRequestLog extends Model
{
    use HasUid;

    public function httpRequest()
    {
        return $this->belongsTo(HttpRequest::class, 'http_request_id');
    }

    public function getRequestDetails()
    {
        return json_decode($this->request_details, true);
    }
}
