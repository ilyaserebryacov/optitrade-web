<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_inn',
        'status',
        'total_amount',
        'manager_id',
        'comment',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Менеджер, ведущий этот заказ
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
