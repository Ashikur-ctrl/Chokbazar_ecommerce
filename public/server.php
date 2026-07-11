<?php
/**
 * Laravel - Web Root Redirect
 * Place this as public_html/index.php to serve Laravel from public/
 */
$publicPath = __DIR__ . '/public';

if (file_exists($publicPath . $_SERVER['REQUEST_URI'])) {
    return false;
}

require_once $publicPath . '/index.php';
