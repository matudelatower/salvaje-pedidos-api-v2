<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone',
        'address',
        'delivery_type',
        'subtotal',
        'total',
        'status',
        'payment_status',
        'mercadopago_preference_id',
        'mercadopago_payment_id',
        'notes'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedTotalAttribute()
    {
        return '$' . number_format($this->total, 2, ',', '.');
    }

    public function getFormattedSubtotalAttribute()
    {
        return '$' . number_format($this->subtotal, 2, ',', '.');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'preparing' => 'primary',
            'ready' => 'success',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }
}
