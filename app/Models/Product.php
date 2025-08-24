<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'sku',
        'barcode',
        'category_id',
        'unit_price',
        'unit',
        'is_active'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // İlişkiler
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function stockRequests(): HasMany
    {
        return $this->hasMany(StockRequest::class);
    }

    public function stockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class);
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySku($query, $sku)
    {
        return $query->where('sku', 'like', "%{$sku}%");
    }

    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    // Toplam stok miktarını hesapla
    public function getTotalStockAttribute()
    {
        return $this->stocks()->sum('quantity');
    }

    // Lokasyon bazında stok miktarını al
    public function getStockAtLocation($locationId)
    {
        return $this->stocks()->where('location_id', $locationId)->first();
    }

    // Stok durumu kontrolü
    public function isLowStock($locationId = null)
    {
        if ($locationId) {
            $stock = $this->getStockAtLocation($locationId);
            return $stock && $stock->quantity <= $stock->min_quantity;
        }

        return $this->stocks()->whereRaw('quantity <= min_quantity')->exists();
    }

    // Stok yeterli mi kontrolü
    public function hasEnoughStock($quantity, $locationId = null)
    {
        if ($locationId) {
            $stock = $this->getStockAtLocation($locationId);
            return $stock && $stock->quantity >= $quantity;
        }

        return $this->getTotalStockAttribute() >= $quantity;
    }
}
