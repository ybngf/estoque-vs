<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'plan_id',
        'status',
        'amount',
        'billing_cycle',
        'starts_at',
        'ends_at',
        'next_billing_date',
        'payment_method',
        'external_id',
        'notes'
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'next_billing_date' => 'date',
        'amount' => 'decimal:2'
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CANCELED = 'canceled';
    const STATUS_PAST_DUE = 'past_due';
    const STATUS_TRIALING = 'trialing';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeTrial($query)
    {
        return $query->where('status', self::STATUS_TRIALING);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_INACTIVE)
                    ->orWhere('ends_at', '<', now());
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->ends_at > now();
    }

    public function isTrial()
    {
        return $this->status === self::STATUS_TRIALING && 
               ($this->ends_at === null || $this->ends_at > now());
    }

    public function isExpired()
    {
        return $this->status === self::STATUS_INACTIVE || 
               $this->ends_at < now();
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELED;
    }

    public function isPastDue()
    {
        return $this->status === self::STATUS_PAST_DUE;
    }

    public function getDaysRemaining()
    {
        return max(0, now()->diffInDays($this->ends_at, false));
    }

    public function getExpirationDate()
    {
        return $this->ends_at;
    }

    public function getFormattedAmount()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_TRIALING => 'Período de Teste',
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_PAST_DUE => 'Pagamento Pendente',
            self::STATUS_CANCELED => 'Cancelado',
            self::STATUS_INACTIVE => 'Inativo',
            default => 'Desconhecido'
        };
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_TRIALING => 'bg-info',
            self::STATUS_ACTIVE => 'bg-success',
            self::STATUS_PAST_DUE => 'bg-warning',
            self::STATUS_CANCELED => 'bg-secondary',
            self::STATUS_INACTIVE => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function renew($months = 1)
    {
        $this->ends_at = Carbon::parse($this->ends_at)->addMonths($months);
        $this->status = self::STATUS_ACTIVE;
        $this->save();

        return $this;
    }

    public function cancel()
    {
        $this->status = self::STATUS_CANCELED;
        $this->save();

        return $this;
    }
}
