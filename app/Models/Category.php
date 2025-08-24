<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // İlişkiler
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    // Ana kategori mi kontrolü
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    // Alt kategori mi kontrolü
    public function isChild(): bool
    {
        return !is_null($this->parent_id);
    }

    // Tüm üst kategorileri al (recursive)
    public function getAllParents()
    {
        $parents = collect();
        $current = $this->parent;

        while ($current) {
            $parents->push($current);
            $current = $current->parent;
        }

        return $parents->reverse();
    }

    // Kategori yolunu al (örn: Gıda > Süt Ürünleri > Peynir)
    public function getFullPathAttribute(): string
    {
        $path = collect([$this->name]);
        $current = $this->parent;

        while ($current) {
            $path->prepend($current->name);
            $current = $current->parent;
        }

        return $path->implode(' > ');
    }

    // Alt kategorileri sayısı
    public function getChildrenCountAttribute(): int
    {
        return $this->children()->count();
    }

    // Ürün sayısı
    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }
}
