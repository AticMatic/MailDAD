<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Acelle\Library\Traits\HasUid;
use Illuminate\Support\Facades\Redis;

class Attachment extends Model
{
    use HasUid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'size'
    ];

    /**
     * Association with campaign through campaign_id column.
     */
    public function campaign()
    {
        return $this->belongsTo('Acelle\Model\Campaign', 'campaign_id');
    }

    public function customer()
    {
        return $this->belongsTo('Acelle\Model\Customer', 'customer_id');
    }

    /**
     * Association with email through email_id column.
     */
    public function email()
    {
        return $this->belongsTo('Acelle\Model\Email', 'email_id');
    }

    /**
     * Remove attachment.
     *
     * @return object
     */
    public function remove()
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
        $this->delete();
    }

    public function getUploadPath($path = null)
    {
        return $this->customer->getAttachmentsPath($path);
    }

    public function upload($file)
    {
        $filename = $file->getClientOriginalName();
        $this->size = $file->getSize();
        $this->name = $filename;

        // save
        $this->save();

        // upload
        $file->move($this->getUploadPath(), $this->uid);

        if (config('custom.distributed_mode')) {
            if (!config('custom.distributed_master')) {
                throw new \Exception('Upload attachment should be executed on MASTER node only');
            }

            $this->cacheToRedis();
        }
    }

    public function getPath()
    {
        return $this->getUploadPath($this->uid);
    }

    /**
     * Store this file in Redis (as cache).
     *
     * @param int $ttl  Time to live in seconds (default 5 min).
     * @return string   The cache ID used to retrieve the file.
     */
    public function cacheToRedis(int $ttl = 1800): string
    {
        $path = $this->getPath();
        $content = file_get_contents($path);

        if ($content === false) {
            throw new \RuntimeException("Failed to read attachment file at: {$path}");
        }

        $id = $this->getFileCacheId();
        $keyBody = "tmp:file:{$id}:body";
        $keyMeta = "tmp:file:{$id}:meta";

        $meta = [
            'mime' => mime_content_type($path) ?: 'application/octet-stream',
            'size' => strlen($content),
        ];

        // Store compressed body + metadata
        Redis::multi()
            ->setex($keyBody, $ttl, gzencode($content, 6))
            ->setex($keyMeta, $ttl, json_encode($meta))
            ->exec();

        return $id;
    }

    /**
     * Fetch a cached file from Redis.
     *
     * @param string $id   The cache ID returned by cacheToRedis().
     * @return array|null  [ 'bytes' => string, 'meta' => array ] or null if expired.
     */
    public function fetchFromRedis(): ?array
    {
        $id = $this->getFileCacheId();

        $keyBody = "tmp:file:{$id}:body";
        $keyMeta = "tmp:file:{$id}:meta";

        $body = Redis::get($keyBody);
        $meta = json_decode(Redis::get($keyMeta) ?? '{}', true);

        if (!$body) {
            return null; // expired or not found
        }

        return [
            'bytes' => gzdecode($body) ?: $body,
            'meta'  => $meta,
        ];
    }

    /**
     * Check if a cached file still exists in Redis.
     *
     * @param  string $id
     * @return bool
     */
    public function cacheExistsInRedis(): bool
    {
        $id = $this->getFileCacheId();
        $keyBody = "tmp:file:{$id}:body";
        return Redis::exists($keyBody) > 0;
    }

    public function getFileCacheId()
    {
        $id = "attachment-{$this->uid}";
        return $id;
    }

    public function makeSwiftAttachmentFromRedis(): ?\Swift_Attachment
    {
        $data = $this->fetchFromRedis();
        if (!$data) {
            return null;
        }

        $bytes = $data['bytes']; // already decoded in fetchFromRedis()
        $mime  = $data['meta']['mime'];

        $attachment = new \Swift_Attachment($bytes, $this->name, $mime);

        return $attachment;
    }

    public function makeSwiftAttachmentFromPath(): ?\Swift_Attachment
    {
        $attachment = \Swift_Attachment::fromPath($this->getPath());
        $attachment->setFilename($this->name);
        return $attachment;
    }

    public function clearCacheFromRedis(): int
    {
        $id = $this->getFileCacheId();
        $keyBody = "tmp:file:{$id}:body";
        $keyMeta = "tmp:file:{$id}:meta";

        return Redis::del([$keyBody, $keyMeta]);
    }
}
