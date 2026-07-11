<?php
$root = realpath(__DIR__.'/..');
$files = ['seo_update.zip', 'extract_seo.php', 'run_seo_migrate.php', 'clean_seo.php'];
foreach ($files as $f) {
    foreach ([__DIR__.'/'.$f, $root.'/'.$f] as $p) {
        if (is_file($p)) { unlink($p); }
    }
}
echo "done";
