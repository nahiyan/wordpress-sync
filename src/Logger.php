<?php

namespace Vivasoft\WpSync;

class Logger
{
    private static $logger = null;
    public static function get()
    {
        if (Logger::$logger == null) {
            Logger::$logger = new \Monolog\Logger('debug');
            $handler = new \Monolog\Handler\StreamHandler(path_join(Config::getBaseDir(), "logs/debug.log"), \Monolog\Logger::INFO);
            $handler->setFormatter(new \Monolog\Formatter\LineFormatter(
                null, // Format of message in log, default [%datetime%] %channel%.%level_name%: %message% %context% %extra%\n
                null, // Datetime format
                true, // allowInlineLineBreaks option, default false
                true// discard empty Square brackets in the end, default false
            ));
            Logger::$logger->pushHandler($handler);
        }

        return Logger::$logger;
    }
    public static function debug($key, $message)
    {
        Logger::get()->info(($key != null ? $key . ": " : "") . $message);
        // $logFilePath = path_join(Config::getBaseDir(), 'logs' . DIRECTORY_SEPARATOR . 'debug.log');
        // error_log(($key != null ? $key . ": " : "") . $message . PHP_EOL, 3, $logFilePath);
    }
    public static function debugJson($key, $json)
    {
        Logger::get()->info($key . ": " . json_encode($json, JSON_PRETTY_PRINT));
    }
}
