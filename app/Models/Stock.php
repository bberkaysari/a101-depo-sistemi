<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'location_id',
        'quantity',
        'min_quantity',
        'max_quantity',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
    ];

    // İlişkiler
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    // Scope'lar
    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= min_quantity');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', 0);
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    // Stok durumu kontrolü
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_quantity;
    }

    public function isOutOfStock(): bool
    {
        return $this->quantity == 0;
    }

    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }

    public function isOverStock(): bool
    {
        return $this->max_quantity && $this->quantity > $this->max_quantity;
    }

    // Stok miktarı güncelleme
    public function addStock(int $quantity): bool
    {
        return $this->increment('quantity', $quantity);
    }

    public function removeStock(int $quantity): bool
    {
        if ($this->quantity >= $quantity) {
            return $this->decrement('quantity', $quantity);
        }
        return false;
    }

    public function setStock(int $quantity): bool
    {
        return $this->update(['quantity' => $quantity]);
    }

    // Stok yeterli mi kontrolü
    public function hasEnoughStock(int $quantity): bool
    {
        return $this->quantity >= $quantity;
    }

    // Stok yüzdesi
    public function getStockPercentageAttribute(): float
    {
        if ($this->max_quantity == 0) {
            return 0;
        }
        return round(($this->quantity / $this->max_quantity) * 100, 2);
    }

    // Stok durumu metni
    public function getStockStatusAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'Stokta Yok';
        } elseif ($this->isLowStock()) {
            return 'Düşük Stok';
        } elseif ($this->isOverStock()) {
            return 'Aşırı Stok';
        } else {
            return 'Normal';
        }
    }
}
