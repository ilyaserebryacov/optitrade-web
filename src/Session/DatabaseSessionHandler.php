<?php

namespace App\Session;

use Illuminate\Database\Capsule\Manager as Capsule;
use SessionHandlerInterface;

class DatabaseSessionHandler implements SessionHandlerInterface
{
    private string $table;

    public function __construct(string $table = 'sessions')
    {
        $this->table = $table;
    }

    public static function register(): void
    {
        $handler = new self();
        session_set_save_handler($handler, true);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $session = Capsule::table($this->table)->where('id', $id)->first();

        if ($session) {
            return (string) $session->payload;
        }

        return '';
    }

    public function write(string $id, string $data): bool
    {
        Capsule::table($this->table)->updateOrInsert(
            ['id' => $id],
            [
                'payload' => $data,
                'last_activity' => time(),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]
        );

        return true;
    }

    public function destroy(string $id): bool
    {
        Capsule::table($this->table)->where('id', $id)->delete();
        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        return Capsule::table($this->table)
            ->where('last_activity', '<', time() - $max_lifetime)
            ->delete();
    }
}
