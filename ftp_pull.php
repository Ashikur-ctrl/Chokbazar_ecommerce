<?php
$server = 'ftp.chokbazar.com';
$username = 'chokbazar';
$password = 'HlO}0,%&,bMa=C&p';
$remoteRoot = '/public_html';
$localRoot = __DIR__ . '/ftp_live';

$ignore = [
    '.env', '.env.*', 'node_modules', 'vendor', 'storage/logs',
    'storage/framework/cache', 'storage/framework/sessions',
    'storage/framework/views', 'bootstrap/cache', 'public/hot',
    '.git', '.vscode', 'ftp_live', 'ftp_pull.php',
];

function shouldIgnore($path, $ignore) {
    foreach ($ignore as $pattern) {
        if (str_starts_with($pattern, '*')) {
            if (str_ends_with($path, substr($pattern, 1))) return true;
        } elseif (str_ends_with($pattern, '/')) {
            if (str_starts_with($path, $pattern) || str_contains($path, '/' . $pattern)) return true;
        } elseif (str_contains($path, $pattern)) {
            return true;
        }
    }
    return false;
}

function syncDir($ftp, $remoteDir, $localDir, $ignore, $remoteRoot) {
    if (!is_dir($localDir)) mkdir($localDir, 0755, true);

    $files = @ftp_nlist($ftp, $remoteDir);
    if (!$files) {
        echo "  [empty] $remoteDir\n";
        return;
    }

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        $localPath = $localDir . '/' . basename($file);
        $relativePath = substr($remoteDir . '/' . basename($file), strlen($remoteRoot) + 1);

        if (shouldIgnore($relativePath, $ignore)) {
            echo "  [skip] $relativePath\n";
            continue;
        }

        if (is_dir($file) || @ftp_chdir($ftp, $file)) {
            ftp_chdir($ftp, $remoteDir); // go back
            syncDir($ftp, $remoteDir . '/' . basename($file), $localPath, $ignore, $remoteRoot);
        } else {
            echo "  [get]  $relativePath\n";
            if (!ftp_get($ftp, $localPath, $remoteDir . '/' . basename($file), FTP_BINARY)) {
                // try ASCII
                ftp_get($ftp, $localPath, $remoteDir . '/' . basename($file), FTP_ASCII);
            }
        }
    }
}

echo "Connecting to $server...\n";
$ftp = ftp_connect($server, 21, 30);
if (!$ftp) { die("Failed to connect\n"); }

if (!ftp_login($ftp, $username, $password)) { die("Login failed\n"); }
ftp_pasv($ftp, true);
echo "Connected. Downloading from $remoteRoot to $localRoot...\n";

syncDir($ftp, $remoteRoot, $localRoot, $ignore, $remoteRoot);

ftp_close($ftp);
echo "\nDone! Files downloaded to: $localRoot\n";
