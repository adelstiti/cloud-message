<?php

namespace MedianetDev\CloudMessage\Drivers;

use MedianetDev\CloudMessage\Contracts\NotificationInterface;

class HuaweiNotification implements NotificationInterface
{
    use Notification;

    private static $urlAuth = 'https://login.vmall.com/oauth2/token';
    private static $pushUrl = 'https://push-api.cloud.huawei.com/v1/';

    public static function sendToAll(array $message, string $os)
    {
        // TODO: Send to all devices
    }

    public static function sendToTokens(array $message, array $tokens, string $os)
    {
        $structureData = [];
        $structureData['validate_only'] = false;
        $structureData['message']['data'] = json_encode($message);
        $structureData['message']['token'] = $tokens;

        $headers = ['Content-Type: application/json', 'Authorization: Bearer '.self::getAccessToken()];

        $url = self::$pushUrl.config('cloud_message.huawei.app_id').'/messages:send';

        $response = self::request($url, json_encode($structureData), $headers);

        return $response;
    }

    // Other required interface methods
    private static function getAccessToken()
    {
        $data = [
            'grant_type' => config('cloud_message.huawei.grant_type'),
            'client_id' => config('cloud_message.huawei.app_id'),
            'client_secret' => config('cloud_message.huawei.app_secret'),
        ];

        $response = self::request(self::$urlAuth, http_build_query($data));
        $result = json_decode($response['data'], true);

        return $result['access_token'] ?? '';
    }

    public static function sendToTopic(array $message, string $topic, string $os)
    {
    }

    public static function subscribeToTopic(string $topic, array $tokens)
    {
    }

    public static function unsubscribeToTopic(string $topic, array $tokens)
    {
    }
}
