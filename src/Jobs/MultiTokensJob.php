<?php

namespace MedianetDev\CloudMessage\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MedianetDev\CloudMessage\Drivers\Notification;

class MultiTokensJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Notification;

    protected $tokens;
    protected $message;
    protected $url;
    protected $headers;

    public function __construct($tokens, $message, $url, $headers)
    {
        $this->tokens = $tokens;
        $this->message = $message;
        $this->url = $url;
        $this->headers = $headers;
    }

    public function handle()
    {
        foreach ($this->tokens as $mobileId) {
            self::request($this->url, json_encode(['message' => [
                'token' => $mobileId,
                'data' => $this->message,
                'notification' => [
                    'title' => $this->message['title'],
                    'body' => $this->message['body'],
                ],
            ]]), $this->headers);
        }
    }
}
