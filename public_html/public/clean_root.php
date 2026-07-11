<?php
$root = realpath(__DIR__ . '/..');
$files = ['migrate.php', 'clean_root.php'];
foreach ($files as $f) {
    $path = $root . '/' . $f;
    if (is_file($path)) {
        unlink($path);
        echo "Deleted: $f\n";
    }
}
echo "done";
