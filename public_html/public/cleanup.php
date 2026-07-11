<?php
$files = ['migrate.php', 'init_storage.php', 'apptest.php', 'cleanup.php', 'phpcheck.php', 'phpinfo.php', 'phpcheck2.php', 'phpcheck3.php'];
$base = __DIR__;
foreach ($files as $f) {
    $path = $base . '/' . $f;
    if (file_exists($path)) {
        unlink($path);
        echo "Deleted: $f\n";
    }
}
echo "Cleanup done";
