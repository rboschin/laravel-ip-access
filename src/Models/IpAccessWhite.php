<?php

namespace Rboschin\LaravelIpAccess\Models;

use Illuminate\Database\Eloquent\Model;

class IpAccessWhite extends Model
{
    protected $fillable = [
        'ip_address',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active IP addresses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get all active IP addresses as an array.
     */
    public static function getActiveIpAddresses(): array
    {
        return self::active()
            ->pluck('ip_address')
            ->filter()
            ->toArray();
    }
}
