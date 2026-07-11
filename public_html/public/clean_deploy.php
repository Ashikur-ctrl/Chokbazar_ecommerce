<?php
$files = ['sync_update.zip', 'deploy_sync.php', 'clean_deploy.php'];
$root = realpath(__DIR__.'/..');
foreach ($files as $f) {
    foreach ([__DIR__.'/'.$f, $root.'/'.$f] as $p) {
        if (is_file($p)) { unlink($p); }
    }
}
echo "done";
