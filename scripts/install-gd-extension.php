<?php

$installer = trim(shell_exec('command -v install-php-extensions 2>/dev/null'));

if ($installer === '') {
    fwrite(STDERR, "install-php-extensions command not found. Skipping GD installation.\n");
    exit(0);
}

if (extension_loaded('gd')) {
    fwrite(STDERR, "GD extension already loaded. Skipping installation.\n");
    exit(0);
}

passthru($installer . ' gd', $exitCode);

if ($exitCode !== 0) {
    fwrite(STDERR, "Failed to install GD extension (exit code {$exitCode}).\n");
}

exit(0);
