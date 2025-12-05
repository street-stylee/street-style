<?php

namespace App\Helpers;

class LoggerHelper {

    private static $log_directory = ROOT . '/logs';

    public static function log(string $level, string $message) {
        try {
            if (!is_dir(self::$log_directory)) {
                mkdir(self::$log_directory, 0775, true);
            }

            $log_file = self::$log_directory . '/log-' . date('Y-m') . '.txt';

            $formatted_message = sprintf(
                "[%s] [%s] %s%s",
                date('Y-m-d H:i:s'),
                strtoupper($level),
                $message,
                PHP_EOL
            );

            file_put_contents($log_file, $formatted_message, FILE_APPEND);

        } catch (\Exception $e) {
            error_log('!! FALHA AO ESCREVER NO LOG: ' . $e->getMessage());
        }
    }
}