<?php
/**
 * FTP Deploy — push local changes to chokbazar.com
 *
 * Usage:
 *   php ftp_deploy.php                    # dry run (quick check)
 *   php ftp_deploy.php --run              # upload changed files
 *   php ftp_deploy.php --run --all        # upload + clear cache + migrate
 */

$server = 'ftp.chokbazar.com';
$username = 'chokbazar';
$password = 'HlO}0,%&,bMa=C&p';
$remoteRoot = '/public_html';
$localRoot = __DIR__;

$ignore = [
    '.env', '.env.*', 'node_modules', 'vendor', 'storage/logs',
    'storage/framework/cache', 'storage/framework/sessions',
    'storage/framework/views', 'bootstrap/cache', 'public/hot',
    '.git', '.vscode', 'ftp_live', 'ftp_pull.php', 'ftp_deploy.php',
    'deploy.sh', '.cpanel.yml', 'stitch_nexus_commerce_os*', 'files*',
];

function shouldIgnore($relativePath, $ignore) {
    foreach ($ignore as $pattern) {
        if (fnmatch($pattern, $relativePath)) return true;
        if (fnmatch($pattern, basename($relativePath))) return true;
        $trimmed = rtrim($pattern, '*');
        if (str_starts_with($relativePath, $trimmed)) return true;
    }
    return false;
}

function getLocalFiles($dir, $ignore, $rootLen) {
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    $files = [];
    foreach ($items as $item) {
        if ($item->isDir()) continue;
        $relative = substr($item->getPathname(), $rootLen);
        $relative = ltrim(str_replace('\\', '/', $relative), '/');
        if ($relative === '' || shouldIgnore($relative, $ignore)) continue;
        $files[] = ['path' => $relative, 'size' => filesize($item->getPathname())];
    }
    return $files;
}

function ensureRemoteDir($ftp, $remotePath, &$createdDirs) {
    if (isset($createdDirs[$remotePath])) return;
    $parts = explode('/', ltrim($remotePath, '/'));
    $build = '';
    foreach ($parts as $p) {
        $build = $build ? $build . '/' . $p : $p;
        if (!isset($createdDirs['/' . $build])) {
            @ftp_mkdir($ftp, '/' . $build);
            $createdDirs['/' . $build] = true;
        }
    }
}

$isDryRun = !in_array('--run', $argv);

echo "Connecting to $server...\n";
$ftp = ftp_connect($server, 21, 10);
if (!$ftp) { die("Failed to connect\n"); }
if (!ftp_login($ftp, $username, $password)) { die("Login failed\n"); }
ftp_pasv($ftp, true);
echo "Connected.\n\n";

$localFiles = getLocalFiles($localRoot, $ignore, strlen($localRoot) + 1);

if ($isDryRun) {
    echo "════════════════════════════════════════════════\n";
    echo " DRY RUN — " . count($localFiles) . " local files tracked\n";
    echo " Add --run to upload\n";
    echo "════════════════════════════════════════════════\n\n";
    $dirs = [];
    foreach ($localFiles as $f) {
        $dir = dirname($f['path']);
        if ($dir !== '.' && !isset($dirs[$dir])) {
            $dirs[$dir] = true;
            echo "   📁 $dir/\n";
        }
    }
    echo "\n " . count($dirs) . " directories, " . count($localFiles) . " files to sync.\n";
    ftp_close($ftp);
    exit(0);
}

echo "════════════════════════════════════════════════\n";
echo " Deploying to $server$remoteRoot ...\n";
echo "════════════════════════════════════════════════\n\n";

$createdDirs = [];
$count = 0;

foreach ($localFiles as $f) {
    $remotePath = $remoteRoot . '/' . $f['path'];
    $localPath = $localRoot . '/' . $f['path'];

    ensureRemoteDir($ftp, dirname($remotePath), $createdDirs);

    echo "  [put] {$f['path']}\n";
    if (!ftp_put($ftp, $remotePath, $localPath, FTP_BINARY)) {
        ftp_put($ftp, $remotePath, $localPath, FTP_ASCII);
    }
    $count++;
}

echo "\n✔ $count file(s) uploaded.\n";

$doMigrate = in_array('--all', $argv);
$doCache = in_array('--all', $argv);

if ($doCache || $doMigrate) {
    echo "\n⚠ Cache clear and migrations need SSH access.\n";
    echo "  The SSH server at chokbazar.com:22 is currently not reachable.\n";
    echo "  Run these manually in cPanel Terminal or when SSH is restored:\n";
    echo "    cd $remoteRoot && php artisan optimize:clear\n";
    echo "    cd $remoteRoot && php artisan migrate --force\n";
}

ftp_close($ftp);
echo "\n════════════════════════════════════════════════\n";
echo " FTP deploy complete.\n";
echo "════════════════════════════════════════════════\n";
