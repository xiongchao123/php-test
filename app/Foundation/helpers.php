<?php

use App\Foundation\Application;
use Monolog\Logger;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string|null $abstract
     * @param bool $new
     * @return mixed|Application
     */
    function app($abstract = null, bool $new = false)
    {
        if (is_null($abstract)) {
            return Application::getInstance();
        }

        return Application::getInstance()->get($abstract, $new);
    }
}

if (!function_exists('logger')) {
    /**
     * Log a debug message to the logs.
     *
     * @param string|null $message
     * @param array $context
     * @return Logger|null
     */
    function logger($message = null, array $context = [])
    {
        if (is_null($message)) {
            return app('log');
        }

        return app('log')->debug($message, $context);
    }
}

if (!function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string|null $key
     * @param mixed $default
     * @return mixed|\Illuminate\Config\Repository
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }

        if (is_array($key)) {
            return app('config')->set($key);
        }

        return app('config')->get($key, $default);
    }
}

if (!function_exists('event')) {
    /**
     * Dispatch an event and call the listeners.
     *
     * @param string|object $event
     * @param bool $halt
     * @return array|null
     */
    function event($event, $halt = false)
    {
        return app('events')->dispatch($event, $halt);
    }
}