<?php
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$input = new Symfony\Component\Console\Input\ArrayInput(['command' => 'migrate', '--force' => true]);
$output = new Symfony\Component\Console\Output\BufferedOutput;
$exitCode = $kernel->handle($input, $output);
$kernel->terminate($input, $exitCode);
echo htmlspecialchars($output->fetch());
echo "\nExit: $exitCode";
