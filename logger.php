<?php

class Logger
{
    public static function debug($key, $message)
    {
        $logFilePath = path_join(BASE_DIR, 'logs' . DIRECTORY_SEPARATOR . 'debug.log');
        error_log(($key != null ? $key . ": " : "") . $message . PHP_EOL, 3, $logFilePath);
    }
    public static function debugJson($key, $json)
    {
        $logFilePath = path_join(BASE_DIR, 'logs' . DIRECTORY_SEPARATOR . 'debug.log');
        $message = json_encode($json, JSON_PRETTY_PRINT);
        error_log(($key != null ? $key . ": " : "") . $message . PHP_EOL, 3, $logFilePath);
    }
}
