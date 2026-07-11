<?php
$files = ['run_migrate.php', 'cleanup2.php', 'htaccess.txt', 'root_cleanup.php', 'init_storage.php', 'migrate.php', 'OrderSplitter.php', 'clean_all.php'];
foreach ($files as $f) {
    $p = __DIR__ . '/../' . $f;
    if (is_file($p)) { unlink($p); echo "Deleted: $f\n"; }
}
$thisFile = __FILE__;
if (is_file($thisFile)) { unlink($thisFile); }
echo "done";
