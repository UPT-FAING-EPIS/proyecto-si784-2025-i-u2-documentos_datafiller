<?php
spl_autoload_register(function (string $class) {
    if ($class === 'App\Config\Database') {
        require __DIR__ . '/Stubs/DatabaseNowStub.php';
        return true;
    }
    return false;
}, true, true);