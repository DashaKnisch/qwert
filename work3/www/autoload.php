<?php

// Автозагрузчик для Clean Architecture
spl_autoload_register(function ($className) {
    // Преобразуем namespace в путь к файлу
    $file = 'src/' . str_replace('\\', '/', $className) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
        return;
    }
    
    // Fallback для старых классов
    $directories = [
        'config/',
        'models/',
        'controllers/',
        'services/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Подключаем старую конфигурацию для совместимости
require_once 'config/Database.php';
require_once 'config/Config.php';