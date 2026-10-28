<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Таблица пользователей и разграничения прав (RBAC)
Capsule::schema()->create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password_hash');
    // Роли пользователей по брифу: admin, manager, storekeeper, client
    $table->enum('role', ['admin', 'manager', 'storekeeper', 'client'])->default('manager');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
