<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Таблица многоскладского учёта и резервирования товаров
Capsule::schema()->create('stocks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
    $table->string('warehouse_name')->default('Центральный склад');
    $table->integer('in_stock')->default(0);    // Фактическое наличие на складе
    $table->integer('reserved')->default(0);    // Количество товара в резерве под заказы
    $table->integer('in_transit')->default(0);   // Количество товара в пути от поставщика
    $table->string('shelf_location')->nullable(); // Ячейка адресного хранения (ряд/полка)
    $table->timestamps();

    // Защита от дублирования одного товара на одном складе
    $table->unique(['product_id', 'warehouse_name']);
});
