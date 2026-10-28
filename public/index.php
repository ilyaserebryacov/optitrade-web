<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Config\Config;
use App\Controllers\OrderController;
use App\Routing\Router;
use App\Session\DatabaseSessionHandler;
use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// 1. Инициализация конфигурации
Config::init();

// 2. Инициализация логирования Monolog
$logger = new Logger(Config::get('logging.channel', 'optitrade'));
$logFile = Config::get('logging.file', dirname(__DIR__) . '/storage/logs/app.log');
$logDir = dirname($logFile);
if (!is_dir($logDir) && !file_exists($logDir)) {
    @mkdir($logDir, 0777, true);
}
$logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));

// 3. Логирование входящего HTTP-запроса (по ДЗ Болотова)
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$rawInput = file_get_contents('php://input');

$logger->info('Incoming HTTP Request', [
    'method' => $requestMethod,
    'uri' => $requestUri,
    'headers' => getallheaders() ?: [],
    'body' => $rawInput,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
]);

// 4. Подключение к БД через Eloquent ORM
$capsule = new Capsule();
$capsule->addConnection(Config::get('db'));
$capsule->setAsGlobal();
$capsule->bootEloquent();

// 5. Запуск кастомных сессий в БД (по ДЗ Болотова от 27.10)
DatabaseSessionHandler::register();

// 6. Инициализация роутера и регистрация маршрутов
$router = new Router();

$router->get('/', function () {
    return [
        'status' => 'success',
        'system' => 'OptiTrade ERP API',
        'environment' => Config::getEnvironment(),
        'session_id' => session_id(),
        'timestamp' => date('Y-m-d H:i:s'),
    ];
});

$router->get('/api/orders', [OrderController::class, 'index']);
$router->post('/api/orders', [OrderController::class, 'store']);

// 7. Обработка запроса и формирование ответа
$response = $router->dispatch($requestMethod, $requestUri);

// 8. Логирование тела ответа (по ДЗ Болотова)
$logger->info('Outgoing HTTP Response', [
    'http_status' => http_response_code(),
    'response' => $response,
]);

// 9. Отправка ответа клиенту
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
