<?php
spl_autoload_register(function($class) {
    $class = str_replace('\\', '/', $class);
    $paths = [
        '../app/core/',
        '../app/controllers/',
        '../app/models/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
}); 