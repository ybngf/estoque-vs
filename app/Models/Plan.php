<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'max_users',
        'max_products',
        'features',
        'active',
        'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'active' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getFormattedPriceAttribute()
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    public function hasUnlimitedUsers()
    {
        return is_null($this->max_users);
    }

    public function hasUnlimitedProducts()
    {
        return is_null($this->max_products);
    }

    public function getFeaturesList()
    {
        return $this->features ?? [];
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }
}
