<?php

spl_autoload_register(
    function ($className) {
        $filename = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
//        echo $filename;die;
        $lib = 'Lib' . DIRECTORY_SEPARATOR;
        if (file_exists($lib . $filename)) {
            require_once $lib . $filename;
        } elseif (file_exists($filename)) {
            require_once $filename;
        }
    }
);