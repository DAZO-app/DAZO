<?php

$storagePath = $_ENV['LARAVEL_STORAGE_PATH']
    ?? $_SERVER['LARAVEL_STORAGE_PATH']
    ?? '/tmp/dazo-testing-storage';

foreach ([
    $storagePath,
    $storagePath . '/app',
    $storagePath . '/app/private',
    $storagePath . '/app/public',
    $storagePath . '/framework',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/testing',
    $storagePath . '/framework/views',
    $storagePath . '/logs',
] as $directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
}

require __DIR__ . '/../vendor/autoload.php';
