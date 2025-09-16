<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'last_login',
        'company_id',
        'avatar',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'last_login' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: auth()->user()?->company_id;
        return $query->where('company_id', $companyId);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isEmployee(): bool
    {
        return $this->hasRole('employee');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function canManageCompany(): bool
    {
        return $this->isAdmin() || $this->isSuperAdmin();
    }

    public function getCompanyPlan()
    {
        return $this->company?->plan;
    }

    public function hasCompanyAccess($companyId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->company_id == $companyId;
    }

    /**
     * Get the user's avatar URL
     */
    public function getAvatarUrl(): string
    {
        if ($this->avatar && \Storage::disk('public')->exists($this->avatar)) {
            return \Storage::disk('public')->url($this->avatar);
        }

        // Placeholder usando UI Avatars
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&size=150&background=6c757d&color=ffffff";
    }

    /**
     * Get the user's thumbnail avatar URL
     */
    public function getThumbnailUrl(): string
    {
        if ($this->avatar) {
            $directory = dirname($this->avatar);
            $filename = basename($this->avatar);
            $thumbnailPath = "{$directory}/thumbnails/thumb_{$filename}";
            
            if (\Storage::disk('public')->exists($thumbnailPath)) {
                return \Storage::disk('public')->url($thumbnailPath);
            }
        }

        // Placeholder menor
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&size=80&background=6c757d&color=ffffff";
    }
}
