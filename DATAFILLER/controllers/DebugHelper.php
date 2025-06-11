<?php
namespace App\Controllers;

class DebugHelper {
    public static function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logDir = __DIR__ . '/../logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        file_put_contents($logDir . '/debug.log', "[$timestamp] $message\n", FILE_APPEND);
    }
}