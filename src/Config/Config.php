<?php

namespace App\Config;

use App\Security\Encryptor;

class Config
{
    private static array $settings = [];
    private static ?Encryptor $encryptor = null;
    private static string $environment = 'development';
    private static bool $isInitialized = false;

    /**
     * Инициализация конфигурации
     */
    public static function init(?string $env = null, ?string $configPath = null): void
    {
        self::$environment = $env ?? getenv('APP_ENV') ?: 'development';
        self::$encryptor = new Encryptor();

        $path = $configPath ?? dirname(__DIR__, 2) . '/config/app.php';

        if (file_exists($path)) {
            $allConfigs = require $path;
            // Берём настройки под текущее окружение (или дефолтные)
            self::$settings = $allConfigs[self::$environment] ?? $allConfigs['default'] ?? [];
        }

        self::$isInitialized = true;
    }

    /**
     * Получение параметра по ключу (поддерживает точечную нотацию, например 'db.password')
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!self::$isInitialized) {
            self::init();
        }

        $segments = explode('.', $key);
        $data = self::$settings;

        foreach ($segments as $segment) {
            if (!is_array($data) || !array_key_exists($segment, $data)) {
                return $default;
            }
            $data = $data[$segment];
        }

        // Если значение зашифровано (начинается с "encrypted:"), расшифровываем его на лету
        if (is_string($data) && str_starts_with($data, 'encrypted:')) {
            $cipherText = substr($data, 10);
            return self::$encryptor->decrypt($cipherText);
        }

        return $data;
    }

    public static function getEnvironment(): string
    {
        return self::$environment;
    }
}
