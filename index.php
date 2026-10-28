<?php

use App\Config\Config;
use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// 1. Автозагрузка классов Composer (если проект запущен локально)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// 2. Инициализация конфигурации
if (class_exists(Config::class)) {
    Config::init();
}

// 3. Настройка логирования Monolog
$logger = new Logger(Config::get('logging.channel', 'optitrade'));
$logFile = Config::get('logging.file', __DIR__ . '/storage/logs/app.log');

// Создаем директорию для логов, если её нет
$logDir = dirname($logFile);
if (!is_dir($logDir) && !file_exists($logDir)) {
    @mkdir($logDir, 0777, true);
}
$logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));

// 4. Логирование входящего HTTP-запроса
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestBody = file_get_contents('php://input');

$logger->info('Incoming HTTP Request', [
    'method' => $requestMethod,
    'uri' => $requestUri,
    'headers' => getallheaders() ?: [],
    'body' => $requestBody,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
]);

// 5. Инициализация подключения к базе данных через Eloquent ORM
$capsule = new Capsule();
$dbConfig = Config::get('db', [
    'driver' => 'pgsql',
    'host' => '127.0.0.1',
    'database' => 'optitrade',
    'username' => 'postgres',
    'password' => 'secret',
    'charset' => 'utf8',
    'prefix' => '',
]);

$capsule->addConnection($dbConfig);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// 6. Формирование тела ответа
$responsePayload = [
    'status' => 'success',
    'application' => Config::get('app.name', 'OptiTrade'),
    'environment' => Config::getEnvironment(),
    'message' => 'OptiTrade API is running with Eloquent ORM & Monolog',
    'timestamp' => date('Y-m-d H:i:s'),
];

$responseBody = json_encode($responsePayload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// 7. Логирование тела ответа
$logger->info('Outgoing HTTP Response', [
    'http_status' => 200,
    'body' => $responsePayload,
]);

// 8. Отправка ответа клиенту
header('Content-Type: application/json; charset=utf-8');
echo $responseBody;
