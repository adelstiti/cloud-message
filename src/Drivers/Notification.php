<?php

namespace MedianetDev\CloudMessage\Drivers;

trait Notification
{
    protected static function firebaseRequest(string $url, array $payload, array $headers = [], $method = 'POST')
    {
        return self::request($url, json_encode($payload), $headers, $method);
    }

    protected static function request(string $url, string $payload, array $headers = [], $method = 'POST')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ('POST' == $method) {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $data = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (config('cloud_message.with_log')) {
            self::logRequest($url, $method, $headers, $payload, $statusCode, $data);
        }

        return [
            'data' => $data,
            'status' => 200 == $statusCode,
        ];
    }

    protected static function logRequest($url, $method, $headers, $payload, $statusCode, $data)
    {
        app('log')->debug(
            "\n------------------- Gateway request --------------------".
                "\n#Url: ".$url.
                "\n#Method: ".$method.
                "\n#Headers: ".json_encode($headers).
                "\n#Data: ".$payload.
                "\n------------------- Gateway response -------------------".
                "\n#Status code: ".$statusCode.
                "\n#Response: ".$data.
                "\n--------------------------------------------------------"
        );
    }
}
