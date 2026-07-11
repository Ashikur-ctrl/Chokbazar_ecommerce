<?php
$root = __DIR__;
foreach (['run_migrate.php', 'cleanup2.php', 'htaccess.txt', 'root_cleanup.php', 'init_storage.php', 'migrate.php', 'OrderSplitter.php', 'clean_all.php'] as $f) {
    $p = $root . '/' . $f;
    if (is_file($p)) { unlink($p); echo "Deleted: $f\n"; }
}
echo "done";
