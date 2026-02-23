<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderHistory extends Model
{
    protected $table = 'order_histories';

    public $timestamps = false;

    public const CREATED_AT = 'created_at';

    protected $fillable = [
        'order_id',
        'order_status_id',
        'comment',
        'notify',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'notify' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }
}
