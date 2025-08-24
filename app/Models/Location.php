<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'address',
        'parent_id',
        'level',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Üst lokasyon ilişkisi
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    // Alt lokasyonlar ilişkisi
    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    // Tüm alt lokasyonlar (recursive)
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    // Stoklar ilişkisi
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    // Giden stok istekleri
    public function outgoingStockRequests()
    {
        return $this->hasMany(StockRequest::class, 'from_location_id');
    }

    // Gelen stok istekleri
    public function incomingStockRequests()
    {
        return $this->hasMany(StockRequest::class, 'to_location_id');
    }

    // Giden stok transferleri
    public function outgoingStockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'from_location_id');
    }

    // Gelen stok transferleri
    public function incomingStockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'to_location_id');
    }

    // Lokasyon tipine göre scope
    public function scopeWarehouses($query)
    {
        return $query->where('type', 'warehouse');
    }

    public function scopeStores($query)
    {
        return $query->where('type', 'store');
    }

    public function scopeBranches($query)
    {
        return $query->where('type', 'branch');
    }

    // Aktif lokasyonlar
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
