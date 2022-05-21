<?php

class Logger
{
    public static function debug($key, $message)
    {
        $log_file = BASE_DIR . '/logs/debug.log';
        error_log(($key != null ? $key . ": " : "") . $message . PHP_EOL, 3, $log_file);
    }
    public static function debugJson($key, $json)
    {
        $log_file = BASE_DIR . '/logs/debug.log';
        $message = json_encode($json, JSON_PRETTY_PRINT);
        error_log(($key != null ? $key . ": " : "") . $message . PHP_EOL, 3, $log_file);
    }
}
