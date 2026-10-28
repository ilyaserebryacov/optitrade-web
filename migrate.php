<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Config;
use Illuminate\Database\Capsule\Manager as Capsule;

// Инициализируем конфигурацию
Config::init();

// Настраиваем соединение с БД через Eloquent Capsule
$capsule = new Capsule();
$capsule->addConnection(Config::get('db'));
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "Starting migrations...\n";

// Сканируем папку с миграциями
$migrationsPath = __DIR__ . '/database/migrations';
$files = glob($migrationsPath . '/*.php');
sort($files);

foreach ($files as $file) {
    $migrationName = basename($file);
    echo "Running migration: {$migrationName}... ";
    
    try {
        require_once $file;
        echo "DONE\n";
    } catch (\Throwable $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }
}

echo "All migrations processed successfully!\n";
