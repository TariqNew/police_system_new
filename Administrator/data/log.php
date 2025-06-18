<?php
$logger = new class {
    public function log($level, $message) {
        $logFile = __DIR__ . '/../logs/app.log';
        $date = date('Y-m-d H:i:s');
        $formatted = "[$date] [$level] $message\n";
        file_put_contents($logFile, $formatted, FILE_APPEND);
    }
};
