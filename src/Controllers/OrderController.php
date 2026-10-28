<?php

namespace App\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Список оптовых заказов
     */
    public function index(): array
    {
        $orders = Order::query()->with('manager')->latest()->limit(50)->get();
        return $this->response($orders);
    }

    /**
     * Создание нового заказа
     */
    public function store(): array
    {
        $data = $this->getJsonBody();

        if (empty($data['customer_name']) || empty($data['customer_inn'])) {
            return $this->error('Поля customer_name и customer_inn обязательны для заполнения', 422);
        }

        $order = Order::create([
            'order_number' => 'OPT-' . date('Ymd') . '-' . rand(100, 999),
            'customer_name' => $data['customer_name'],
            'customer_inn' => $data['customer_inn'],
            'total_amount' => $data['total_amount'] ?? 0.00,
            'manager_id' => $data['manager_id'] ?? 1,
            'status' => 'new',
            'comment' => $data['comment'] ?? null,
        ]);

        return $this->response($order, 201);
    }
}
