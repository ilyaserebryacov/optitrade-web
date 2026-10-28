<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Таблица номенклатуры (каталог товаров с артикулами и штрихкодами)
Capsule::schema()->create('products', function (Blueprint $table) {
    $table->id();
    $table->string('sku')->unique(); // Артикул
    $table->string('name');          // Наименование товара
    $table->string('barcode')->nullable()->index(); // Штрихкод
    $table->string('category')->nullable();
    $table->decimal('base_price', 12, 2)->default(0.00); // Базовая отпускная цена
    $table->decimal('purchase_price', 12, 2)->default(0.00); // Закупочная стоимость (скрывается от менеджеров)
    $table->timestamps();
});
