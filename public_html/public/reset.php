<?php
$views = glob(__DIR__.'/../storage/framework/views/*.php');
foreach ($views as $v) { unlink($v); }
$cache = glob(__DIR__.'/../storage/framework/cache/data/*.php');
foreach ($cache as $c) { unlink($c); }
file_put_contents(__DIR__.'/../storage/logs/laravel.log', '');
echo 'reset done';
