<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'ingredients',
        'price',
        'no_stock',
        'discount_percentage',
        'discount_start',
        'discount_end',
        'active',
        'category_id',
        'unit_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
        'no_stock' => 'boolean',
        'active' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFinalPriceAttribute()
    {
        if ($this->hasDiscount()) {
            return $this->price * (1 - $this->discount_percentage / 100);
        }
        return $this->price;
    }

    public function hasDiscount()
    {
        return $this->discount_percentage && 
               $this->discount_start && 
               $this->discount_end && 
               now()->between($this->discount_start, $this->discount_end);
    }
}
