<?php

namespace App\Controllers;

abstract class Controller
{
    /**
     * Получение распарсенного тела входящего JSON-запроса
     */
    protected function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?: [];
    }

    /**
     * Формирование успешного ответа
     */
    protected function response(mixed $data, int $status = 200): array
    {
        http_response_code($status);
        return [
            'status' => 'success',
            'data' => $data,
        ];
    }

    /**
     * Формирование ответа с ошибкой
     */
    protected function error(string $message, int $status = 400): array
    {
        http_response_code($status);
        return [
            'status' => 'error',
            'message' => $message,
        ];
    }
}
