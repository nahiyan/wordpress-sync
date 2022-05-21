<?php

class Logger
{
    static function debug($message)
    {
        $log_file = BASE_DIR . '/logs/debug.log';
        error_log($message . PHP_EOL, 3, $log_file);
    }
}
