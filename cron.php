<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Config;
use App\Models\Order;
use App\Models\Stock;
use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

echo "[" . date('Y-m-d H:i:s') . "] OptiTrade Cron Runner started...\n";

// 1. Инициализация конфига и БД
Config::init();
$capsule = new Capsule();
$capsule->addConnection(Config::get('db'));
$capsule->setAsGlobal();
$capsule->bootEloquent();

// 2. Логгер для фоновых задач
$logger = new Logger('cron');
$logger->pushHandler(new StreamHandler(dirname(__DIR__) . '/storage/logs/cron.log', Logger::INFO));

// ЗАДАЧА 1: Очистка зависших броней заказов старше 3 дней
try {
    $expiredOrders = Order::where('status', 'new')
        ->where('created_at', '<', date('Y-m-d H:i:s', strtotime('-3 days')))
        ->get();

    foreach ($expiredOrders as $order) {
        $order->status = 'cancelled';
        $order->comment = ($order->comment ? $order->comment . "\n" : '') . 'Автоматически отменен cron: истек срок оплаты';
        $order->save();

        $logger->info("Order #{$order->order_number} cancelled due to expiration");
        echo "Cancelled expired order: {$order->order_number}\n";
    }
} catch (\Throwable $e) {
    $logger->error("Error cancelling expired orders: " . $e->getMessage());
}

// ЗАДАЧА 2: Очистка устаревших сессий из базы данных (старше 30 дней)
try {
    $deletedSessions = Capsule::table('sessions')
        ->where('last_activity', '<', time() - (30 * 86400))
        ->delete();

    if ($deletedSessions > 0) {
        $logger->info("Cleaned up {$deletedSessions} old sessions");
        echo "Cleaned {$deletedSessions} expired sessions.\n";
    }
} catch (\Throwable $e) {
    $logger->error("Error cleaning sessions: " . $e->getMessage());
}

echo "[" . date('Y-m-d H:i:s') . "] OptiTrade Cron Runner finished successfully.\n";
