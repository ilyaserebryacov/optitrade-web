<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Таблица оптовых заказов клиентов со статусной воронкой
Capsule::schema()->create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number')->unique(); // Номер заказа (например, OPT-2026-001)
    $table->string('customer_name');          // Наименование контрагента (ООО/ИП)
    $table->string('customer_inn', 12);       // ИНН контрагента для валидации
    
    // Статусная модель по ТЗ: новый, подтвержден, комплектация, отгружен, отменен
    $table->enum('status', [
        'new',
        'confirmed',
        'assembling',
        'shipped',
        'cancelled'
    ])->default('new');

    $table->decimal('total_amount', 14, 2)->default(0.00); // Общая сумма заказа
    $table->foreignId('manager_id')->constrained('users'); // Ответственный менеджер
    $table->text('comment')->nullable();
    $table->timestamps();
});
