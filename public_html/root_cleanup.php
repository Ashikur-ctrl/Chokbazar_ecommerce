<?php
$files = ['run_migrate.php', 'cleanup2.php', 'htaccess.txt'];
$base = __DIR__;
foreach ($files as $f) {
    $path = $base . '/' . $f;
    if (is_file($path)) {
        unlink($path);
        echo "Deleted: $f\n";
    }
}
echo "done";
