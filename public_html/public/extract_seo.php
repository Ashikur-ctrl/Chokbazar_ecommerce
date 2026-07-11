<?php
$zip = new ZipArchive;
if ($zip->open(__DIR__.'/../seo_update.zip') === TRUE) {
    $zip->extractTo(__DIR__.'/..');
    $zip->close();
    echo 'extracted';
} else {
    echo 'failed';
}
