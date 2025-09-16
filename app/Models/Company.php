<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'document', // CNPJ
        'address',
        'plan_id',
        'status',
        'trial_ends_at',
        'settings',
        'logo',
        'domain',
        'stripe_customer_id',
        'pagseguro_customer_id',
        'payment_methods',
    ];

    protected $casts = [
        'trial_ends_at' => 'date',
        'settings' => 'array',
        'payment_methods' => 'array',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function getFormattedDocumentAttribute()
    {
        if (!$this->document) return null;
        
        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($this->document, 0, 2),
            substr($this->document, 2, 3),
            substr($this->document, 5, 3),
            substr($this->document, 8, 4),
            substr($this->document, 12, 2)
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isTrial()
    {
        return $this->status === 'trial';
    }

    public function isTrialing()
    {
        return $this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function canAddUsers()
    {
        if ($this->plan->hasUnlimitedUsers()) {
            return true;
        }

        return $this->users()->count() < $this->plan->max_users;
    }

    public function canAddProducts()
    {
        if ($this->plan->hasUnlimitedProducts()) {
            return true;
        }

        return $this->products()->count() < $this->plan->max_products;
    }

    public function getCurrentUsersCount()
    {
        return $this->users()->count();
    }

    public function getCurrentProductsCount()
    {
        return $this->products()->count();
    }

    public function getUsagePercentage($type = 'users')
    {
        if ($type === 'users') {
            if ($this->plan->hasUnlimitedUsers()) return 0;
            return ($this->getCurrentUsersCount() / $this->plan->max_users) * 100;
        }

        if ($type === 'products') {
            if ($this->plan->hasUnlimitedProducts()) return 0;
            return ($this->getCurrentProductsCount() / $this->plan->max_products) * 100;
        }

        return 0;
    }

    /**
     * Get the company's logo URL
     */
    public function getLogoUrl(): string
    {
        if ($this->logo && \Storage::disk('public')->exists($this->logo)) {
            return \Storage::disk('public')->url($this->logo);
        }

        // Placeholder usando nome da empresa
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&size=200&background=007bff&color=ffffff&format=png";
    }

    /**
     * Get the company's logo thumbnail URL
     */
    public function getThumbnailUrl(): string
    {
        if ($this->logo) {
            $directory = dirname($this->logo);
            $filename = basename($this->logo);
            $thumbnailPath = "{$directory}/thumbnails/thumb_{$filename}";
            
            if (\Storage::disk('public')->exists($thumbnailPath)) {
                return \Storage::disk('public')->url($thumbnailPath);
            }
        }

        // Placeholder menor
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&size=60&background=007bff&color=ffffff&format=png";
    }
}
