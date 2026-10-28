<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'product_id',
        'warehouse_name',
        'in_stock',
        'reserved',
        'in_transit',
        'shelf_location',
    ];

    protected $casts = [
        'in_stock' => 'integer',
        'reserved' => 'integer',
        'in_transit' => 'integer',
    ];

    /**
     * Привязка остатка к конкретному товару
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Расчет доступного остатка для продажи по ТЗ: Наличие - Резерв + В пути
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->in_stock - $this->reserved + $this->in_transit);
    }
}
