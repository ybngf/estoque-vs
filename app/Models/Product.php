<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'sku', // Stock Keeping Unit (was 'code')
        'barcode', // EAN/UPC barcode
        'name',
        'brand',
        'description',
        'category_id',
        'supplier_id',
        'cost_price',
        'price', // Selling price (was 'sale_price')
        'last_purchase_price',
        'last_purchase_date',
        'quantity_on_hand', // Current stock (was 'stock_quantity')
        'reorder_point', // Minimum stock (was 'minimum_stock')
        'maximum_stock',
        'unit_of_measure', // (was 'unit_measure')
        'weight',
        'dimensions',
        'location',
        'image', // Product image
        'ai_tags',
        'attributes',
        'is_active', // (was 'active')
        'status',
        'company_id',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'price' => 'decimal:2', // (was 'sale_price')
        'last_purchase_price' => 'decimal:2',
        'last_purchase_date' => 'date',
        'quantity_on_hand' => 'integer', // (was 'stock_quantity')
        'reorder_point' => 'integer', // (was 'minimum_stock')
        'maximum_stock' => 'integer',
        'weight' => 'decimal:3',
        'is_active' => 'boolean', // (was 'active')
        'attributes' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: auth()->user()?->company_id;
        return $query->where('company_id', $companyId);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity_on_hand', '<=', 'reorder_point');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity_on_hand', 0);
    }

    public function scopeDiscontinued($query)
    {
        return $query->where('status', 'discontinued');
    }

    public function isLowStock(): bool
    {
        return $this->quantity_on_hand <= $this->reorder_point;
    }

    public function isOutOfStock(): bool
    {
        return $this->quantity_on_hand == 0;
    }

    public function isActive(): bool
    {
        return $this->is_active && $this->status === 'active';
    }

    public function isDiscontinued(): bool
    {
        return $this->status === 'discontinued';
    }

    // Professional business logic methods
    public function getStockValue(): float
    {
        return $this->quantity_on_hand * $this->cost_price;
    }

    public function getRetailValue(): float
    {
        return $this->quantity_on_hand * $this->price;
    }

    public function getProfitMargin(): float
    {
        if ($this->cost_price == 0) return 0;
        return (($this->price - $this->cost_price) / $this->cost_price) * 100;
    }

    public function getStockStatus(): string
    {
        if ($this->isOutOfStock()) return 'out_of_stock';
        if ($this->isLowStock()) return 'low_stock';
        if ($this->maximum_stock && $this->quantity_on_hand >= $this->maximum_stock) return 'overstock';
        return 'in_stock';
    }

    // Image URL methods
    public function getImageUrl(): string
    {
        if ($this->image && \Storage::disk('public')->exists($this->image)) {
            return \Storage::disk('public')->url($this->image);
        }
        
        return 'https://via.placeholder.com/400x400/e9ecef/6c757d?text=Produto';
    }

    public function getThumbnailUrl(): string
    {
        if ($this->image) {
            $thumbnailPath = 'thumbnails/' . $this->image;
            if (\Storage::disk('public')->exists($thumbnailPath)) {
                return \Storage::disk('public')->url($thumbnailPath);
            }
        }
        
        return 'https://via.placeholder.com/300x300/e9ecef/6c757d?text=Produto';
    }
}
