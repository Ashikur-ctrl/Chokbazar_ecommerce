<?php
$public = __DIR__;
$root = realpath(__DIR__ . '/..');
$files = ['clean_root.php', 'cleaner.php', 'clear_log.php', 'reset.php', 'ver.php', 'test.php', 'test.html', 'test.txt', 'clean_all.php', 'clean_final.php'];
foreach ($files as $f) {
    $p = $public . '/' . $f;
    if (is_file($p)) { unlink($p); }
}
echo "done";
