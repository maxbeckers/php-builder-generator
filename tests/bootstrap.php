<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Clean up any test files from previous runs
$testOutputDir = __DIR__ . '/output';
if (is_dir($testOutputDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($testOutputDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }

    rmdir($testOutputDir);
}
