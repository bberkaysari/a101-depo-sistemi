<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'from_location_id',
        'to_location_id',
        'requested_quantity',
        'approved_quantity',
        'status',
        'request_notes',
        'response_notes',
        'requested_by',
        'responded_by',
        'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    // İlişkiler
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function stockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class);
    }

    // Scope'lar
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByFromLocation($query, $locationId)
    {
        return $query->where('from_location_id', $locationId);
    }

    public function scopeByToLocation($query, $locationId)
    {
        return $query->where('to_location_id', $locationId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Durum kontrolü
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    // İstek onaylama ve stok transferi
    public function approve(int $approvedQuantity, string $responseNotes = null, int $respondedBy = null): bool
    {
        // Stok transferini gerçekleştir
        $this->transferStock($approvedQuantity);
        
        return $this->update([
            'status' => 'approved',
            'approved_quantity' => $approvedQuantity,
            'response_notes' => $responseNotes,
            'responded_by' => $respondedBy,
            'responded_at' => now()
        ]);
    }

    // Stok transferi gerçekleştir
    private function transferStock(int $quantity): void
    {
        // Gönderen lokasyondan stok düş
        $fromStock = Stock::where('product_id', $this->product_id)
            ->where('location_id', $this->from_location_id)
            ->first();

        if ($fromStock && $fromStock->quantity >= $quantity) {
            $fromStock->decrement('quantity', $quantity);
        }

        // Alıcı lokasyona stok ekle
        $toStock = Stock::where('product_id', $this->product_id)
            ->where('location_id', $this->to_location_id)
            ->first();

        if ($toStock) {
            $toStock->increment('quantity', $quantity);
        } else {
            // Eğer alıcı lokasyonda stok yoksa yeni kayıt oluştur
            Stock::create([
                'product_id' => $this->product_id,
                'location_id' => $this->to_location_id,
                'quantity' => $quantity,
                'min_quantity' => 0,
                'max_quantity' => null,
                'notes' => "Transfer edilen stok - İstek #{$this->id}"
            ]);
        }
    }

    // İstek reddetme
    public function reject(string $responseNotes = null, int $respondedBy = null): bool
    {
        // Eğer istek daha önce onaylanmışsa, stokları geri al
        if ($this->status === 'approved' && $this->approved_quantity > 0) {
            $this->reverseStockTransfer();
        }

        return $this->update([
            'status' => 'rejected',
            'response_notes' => $responseNotes,
            'responded_by' => $respondedBy,
            'responded_at' => now()
        ]);
    }

    // Stok transferini geri al
    private function reverseStockTransfer(): void
    {
        // Alıcı lokasyondan stok düş
        $toStock = Stock::where('product_id', $this->product_id)
            ->where('location_id', $this->to_location_id)
            ->first();

        if ($toStock && $toStock->quantity >= $this->approved_quantity) {
            $toStock->decrement('quantity', $this->approved_quantity);
        }

        // Gönderen lokasyona stok geri ekle
        $fromStock = Stock::where('product_id', $this->product_id)
            ->where('location_id', $this->from_location_id)
            ->first();

        if ($fromStock) {
            $fromStock->increment('quantity', $this->approved_quantity);
        }
    }

    // İstek tamamlama
    public function complete(): bool
    {
        return $this->update(['status' => 'completed']);
    }

    // Onaylanan miktar kontrolü
    public function getApprovedQuantityAttribute($value): int
    {
        return $value ?? $this->requested_quantity;
    }

    // İstek süresi (gün)
    public function getRequestAgeAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    // Yanıt süresi (gün)
    public function getResponseTimeAttribute(): ?int
    {
        if (!$this->responded_at) {
            return null;
        }
        return $this->created_at->diffInDays($this->responded_at);
    }
}
