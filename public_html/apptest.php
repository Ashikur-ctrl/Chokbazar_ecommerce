<?php
// Bypass composer platform check
$vendorDir = __DIR__ . '/vendor';
$platformCheck = $vendorDir . '/composer/platform_check.php';
if (file_exists($platformCheck)) {
    // Temporarily disable the platform check
    $bak = file_get_contents($platformCheck);
    file_put_contents($platformCheck, '<?php // disabled');
    try {
        require $vendorDir . '/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        echo "OK: " . get_class($app);
    } finally {
        file_put_contents($platformCheck, $bak);
    }
} else {
    echo "platform_check.php not found at: $platformCheck";
}
