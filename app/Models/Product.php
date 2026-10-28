<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'sku',
        'name',
        'barcode',
        'category',
        'base_price',
        'purchase_price',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
    ];

    /**
     * Остатки данного товара на разных складах
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'product_id');
    }
}
