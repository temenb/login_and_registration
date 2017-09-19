<?php
spl_autoload_register(
    function ($className) {
        $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;
        $filename = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

        if (file_exists($baseDir . $filename)) {
            require_once $baseDir . $filename;
        }
    }
);
