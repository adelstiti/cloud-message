<?php

namespace MedianetDev\CloudMessage\Contracts;

interface NotificationInterface
{
    /**
     * Send a notification message to all devices for a given operating system.
     *
     * @param  array  $message  the notification message data
     * @param  string  $os  The operating system ('android', 'ios', etc.).
     */
    public static function sendToAll(array $message, string $os);

    /**
     * Send a notification message to a specific topic for a given operating system.
     *
     * @param  array  $message  the notification message data
     * @param  string  $topic  the topic to send the message to
     * @param  string  $os  The operating system ('android', 'ios', etc.).
     */
    public static function sendToTopic(array $message, string $topic, string $os);

    /**
     * Send a notification message to specific device tokens for a given operating system.
     *
     * @param  array  $message  the notification message data
     * @param  array  $tokens  the device tokens to send the message to
     * @param  string  $os  The operating system ('android', 'ios', etc.).
     */
    public static function sendToTokens(array $message, array $tokens, string $os);

    /**
     * Subscribe device tokens to a specific topic.
     *
     * @param  string  $topic  the topic to subscribe to
     * @param  array  $tokens  the device tokens to subscribe
     */
    public static function subscribeToTopic(string $topic, array $tokens);

    /**
     * Unsubscribe device tokens from a specific topic.
     *
     * @param  string  $topic  the topic to unsubscribe from
     * @param  array  $tokens  the device tokens to unsubscribe
     */
    public static function unsubscribeToTopic(string $topic, array $tokens);

    // etc
}
