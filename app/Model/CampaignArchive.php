<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;

class CampaignArchive extends Model
{
    protected $casts = [
        'created_at' => 'datetime',
        'open_at' => 'datetime',
        'click_at' => 'datetime',
        'bounce_at' => 'datetime',
        'feedback_at' => 'datetime',
        'unsubscribe_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function openIpLocation()
    {
        return $this->belongsTo('Acelle\Model\IpLocation', 'open_ip_address', 'ip_address');
    }

    public function getSendingServer()
    {
        return SendingServer::find($this->sending_server_id);
    }
}
