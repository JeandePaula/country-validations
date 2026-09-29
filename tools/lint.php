<?php

$root = dirname(__DIR__);
$status = 0;
foreach (['src', 'tests', 'tools'] as $directory) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $directory));
    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname()) . ' 2>&1', $output, $code);
            if ($code !== 0) {
                echo implode(PHP_EOL, $output), PHP_EOL;
                $status = 1;
            }
            $output = [];
        }
    }
}
if ($status === 0) {
    echo "All PHP files passed syntax checks.", PHP_EOL;
}
exit($status);
