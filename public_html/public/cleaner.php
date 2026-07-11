<?php
$root = realpath(__DIR__ . '/..');
foreach (['migrate.php', 'OrderSplitter.php', 'clean_root.php', 'cleaner.php'] as $f) {
    $p = $root . '/' . $f;
    if (is_file($p)) { unlink($p); echo "$f "; }
}
foreach (['cleaner.php'] as $f) {
    $p = __DIR__ . '/' . $f;
    if (is_file($p)) { unlink($p); }
}
echo "done";
