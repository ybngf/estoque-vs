<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'transaction_id', // Unique transaction identifier
        'product_id',
        'user_id',
        'approved_by',
        'type', // entry, exit, adjustment (legacy)
        'transaction_type', // purchase, sale, transfer, adjustment, return, damaged, expired
        'quantity_moved', // Quantity moved (was 'quantity')
        'quantity_before', // Quantity before transaction (was 'previous_quantity')
        'quantity_after', // Quantity after transaction (was 'current_quantity')
        'unit_cost', // Cost per unit
        'total_cost', // Total transaction cost
        'transaction_date', // Business transaction date
        'document_type', // Invoice, PO, etc.
        'reference_number', // PO number, Invoice number, etc.
        'description',
        'memo', // Notes/memo (was 'notes')
        'approved_at',
        'company_id',
    ];

    protected $casts = [
        'quantity_moved' => 'integer', // (was 'quantity')
        'quantity_before' => 'integer', // (was 'previous_quantity')
        'quantity_after' => 'integer', // (was 'current_quantity')
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'transaction_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: auth()->user()?->company_id;
        return $query->where('company_id', $companyId);
    }

    // Legacy scopes for backward compatibility
    public function scopeEntry($query)
    {
        return $query->where('type', 'entry');
    }

    public function scopeExit($query)
    {
        return $query->where('type', 'exit');
    }

    public function scopeAdjustment($query)
    {
        return $query->where('type', 'adjustment');
    }

    // Professional scopes
    public function scopePurchase($query)
    {
        return $query->where('transaction_type', 'purchase');
    }

    public function scopeSale($query)
    {
        return $query->where('transaction_type', 'sale');
    }

    public function scopeTransfer($query)
    {
        return $query->where('transaction_type', 'transfer');
    }

    public function scopeReturn($query)
    {
        return $query->where('transaction_type', 'return');
    }

    public function scopeDamaged($query)
    {
        return $query->where('transaction_type', 'damaged');
    }

    public function scopeExpired($query)
    {
        return $query->where('transaction_type', 'expired');
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'entry' => 'Entrada',
            'exit' => 'Saída',
            'adjustment' => 'Ajuste',
            default => 'Desconhecido'
        };
    }

    public function getTransactionTypeNameAttribute(): string
    {
        return match($this->transaction_type) {
            'purchase' => 'Compra',
            'sale' => 'Venda',
            'transfer' => 'Transferência',
            'adjustment' => 'Ajuste',
            'return' => 'Devolução',
            'damaged' => 'Danificado',
            'expired' => 'Vencido',
            default => 'Outros'
        };
    }

    public function isApproved(): bool
    {
        return !is_null($this->approved_at);
    }

    public function isPending(): bool
    {
        return is_null($this->approved_at);
    }

    // Calculate impact on inventory value
    public function getInventoryImpact(): float
    {
        if (!$this->unit_cost) return 0;
        
        return match($this->transaction_type) {
            'purchase', 'return' => $this->quantity_moved * $this->unit_cost,
            'sale', 'damaged', 'expired' => -($this->quantity_moved * $this->unit_cost),
            'adjustment' => ($this->quantity_after - $this->quantity_before) * $this->unit_cost,
            default => 0
        };
    }
}
