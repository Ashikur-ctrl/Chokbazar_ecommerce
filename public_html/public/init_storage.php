<?php
$base = realpath(__DIR__ . '/..');
$dirs = [
    $base . '/storage/framework/cache/data',
    $base . '/storage/framework/sessions',
    $base . '/storage/framework/views',
    $base . '/storage/framework/testing',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created: $dir\n";
    } else {
        echo "Exists: $dir\n";
    }
}
// Set permissions
exec('chmod -R 755 ' . __DIR__ . '/../storage/framework');
echo "Done";
