<?php

namespace MedianetDev\CloudMessage\Facade;

class CloudMessage
{
    public static function sendToAll(array $message, string $os)
    {
        // Get driver by OS
        $driver = self::getDriverByOs($os);

        // Detect driver class
        $class = self::getDriverClass($driver);

        // Call driver method
        return $class::sendToAll($message, $os);
    }

    public static function sendToTokens(array $message, array $tokens, string $os)
    {
        // Get driver by OS
        $driver = self::getDriverByOs($os);

        // Detect driver class
        $class = self::getDriverClass($driver);

        // Call driver method
        return $class::sendToTokens($message, $tokens, $os);
    }

    public static function sendToTopic(array $message, string $topic, string $os)
    {
        // Get driver by OS
        $driver = self::getDriverByOs($os);

        // Detect driver class
        $class = self::getDriverClass($driver);

        // Call driver method
        return $class::sendToTopic($message, $topic, $os);
    }

    public static function subscribeToTopic(string $topic, array $tokens, string $os)
    {
        // Get driver by OS
        $driver = self::getDriverByOs($os);

        // Detect driver class
        $class = self::getDriverClass($driver);

        // Call driver method
        return $class::subscribeToTopic($topic, $tokens);
    }

    public static function unsubscribeToTopic(string $topic, array $tokens, string $os)
    {
        // Get driver by OS
        $driver = self::getDriverByOs($os);

        // Detect driver class
        $class = self::getDriverClass($driver);

        // Call driver method
        return $class::unsubscribeToTopic($topic, $tokens);
    }

    protected static function getDriverClass(string $driver)
    {
        $drivers = [
            'firebase' => 'MedianetDev\CloudMessage\Drivers\FirebaseNotification',
            'huawei' => 'MedianetDev\CloudMessage\Drivers\HuaweiNotification',
        ];

        if (! array_key_exists($driver, $drivers)) {
            throw new \Exception('Driver not found');
        }

        return $drivers[$driver];
    }

    protected static function getDriverByOs(string $os)
    {
        $os = strtolower($os);
        $osTypes = config('cloud_message.os_types');

        switch ($os) {
            case strtolower($osTypes['android']):
            case strtolower($osTypes['ios']):
                return 'firebase';
            case strtolower($osTypes['huawei']):
                return 'huawei';
            default:
                throw new \InvalidArgumentException('OS type not supported');
                throw new \Exception("OS type '{$os}' is not supported. Allowed values are: {$allowed}");
        }
    }
}
