<?php

$installer = trim(shell_exec('command -v install-php-extensions 2>/dev/null'));

if ($installer === '') {
    fwrite(STDERR, "install-php-extensions command not found. Skipping GD installation.\n");
    return;
}

if (extension_loaded('gd')) {
    fwrite(STDERR, "GD extension already loaded. Skipping installation.\n");
    return;
}

passthru($installer . ' gd', $exitCode);

if ($exitCode !== 0) {
    fwrite(STDERR, "Failed to install GD extension (exit code {$exitCode}).\n");
}
