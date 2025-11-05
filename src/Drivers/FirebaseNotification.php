<?php

namespace MedianetDev\CloudMessage\Drivers;

use Google\Client;
use MedianetDev\CloudMessage\Contracts\NotificationInterface;
use MedianetDev\CloudMessage\Jobs\MultiTokensJob;

class FirebaseNotification implements NotificationInterface
{
    use Notification;

    private static $firebaseApiBaseUrl = 'https://fcm.googleapis.com/v1/projects/';
    private static $firebaseMessagingScope = 'https://www.googleapis.com/auth/firebase.messaging';
    private static $googleApiBaseUrl = 'https://iid.googleapis.com/iid/v1/';

    public static function sendToAll(array $message, string $os)
    {
        // TODO: Send to all devices
    }

    public static function sendToTopic(array $message, string $topic, string $os)
    {
        $headers = [
            'Authorization: Bearer '.self::getAccessToken(),
            'Content-Type: application/json',
        ];

        $url = self::$firebaseApiBaseUrl.config('cloud_message.firebase.project_id').'/messages:send';

        if (isset($message['data'])) {
            $message['data'] = json_encode($message['data'], JSON_UNESCAPED_UNICODE);
        }

        $data = [
            'message' => [
                'topic' => $topic,
                'data' => $message,
            ],
        ];

        if (0 === strcasecmp($os, config('cloud_message.os_types.ios'))) {
            $data['message']['notification'] = [
                'title' => $message['title'],
                'body' => $message['body'],
            ];
        }

        $response = self::request($url, json_encode($data), $headers);

        return ['status' => $response['status']];
    }

    public static function sendToTokens(array $message, array $tokens, string $os)
    {
        $url = self::$firebaseApiBaseUrl.config('cloud_message.firebase.project_id').'/messages:send';
        try {
            if (isset($message['data'])) {
                $message['data'] = json_encode($message['data'], JSON_UNESCAPED_UNICODE);
            }

            $headers = [
                'Authorization: Bearer '.self::getAccessToken(),
                'Content-Type: application/json',
            ];
            if (config('cloud_message.async_requests')) {
                dispatch(new MultiTokensJob($tokens, $message, $url, $headers));
            } else {
                foreach ($tokens as $mobileId) {
                    self::request($url, json_encode(['message' => [
                        'token' => $mobileId,
                        'data' => $message,
                        'notification' => [
                            'title' => $message['title'],
                            'body' => $message['body'],
                        ],
                    ]]), $headers);
                }
            }

            return [
                'status' => true,
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
            ];
        }
    }

    public static function subscribeToTopic(string $topic, array $tokens)
    {
        $url = self::$googleApiBaseUrl.':batchAdd';

        $headers = [
            'Authorization: Bearer '.self::getAccessToken(),
            'Content-Type: application/json',
            'access_token_auth: true',
        ];

        $success = true;
        $chunkedTokens = array_chunk($tokens, 500);

        foreach ($chunkedTokens as $tokenGroup) {
            $data = [
                'to' => '/topics/'.$topic,
                'registration_tokens' => $tokenGroup,
            ];

            $response = self::request($url, json_encode($data), $headers);
            $statusCode = $response['status'];
            if (200 != $statusCode) {
                $success = false;
            }
        }

        return [
            'status' => $success,
        ];
    }

    public static function unsubscribeToTopic(string $topic, array $tokens)
    {
        $url = self::$googleApiBaseUrl.':batchRemove';

        $headers = [
            'Authorization: Bearer '.self::getAccessToken(),
            'Content-Type: application/json',
            'access_token_auth: true',
        ];

        $success = true;
        $chunkedTokens = array_chunk($tokens, 500);

        foreach ($chunkedTokens as $tokenGroup) {
            $data = [
                'to' => '/topics/'.$topic,
                'registration_tokens' => $tokenGroup,
            ];

            $response = self::request($url, json_encode($data), $headers);
            $statusCode = $response['status'];

            if (200 != $statusCode) {
                $success = false;
            }
        }

        return [
            'status' => $success,
        ];
    }

    // Other required interface methods
    private static function getAccessToken()
    {
        $client = new Client();
        $client->setAuthConfig(config('cloud_message.firebase.path_to_service_account'));
        $client->addScope(self::$firebaseMessagingScope);
        $client->useApplicationDefaultCredentials();
        $token = $client->fetchAccessTokenWithAssertion();

        return $token['access_token'] ?? '';
    }
}
