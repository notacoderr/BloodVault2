<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodBank extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blood_banks';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'STOCK_ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'donor',
        'blood_type',
        'acquisition_date',
        'expiration_date',
        'quantity',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'acquisition_date' => 'date',
        'expiration_date' => 'date',
        'quantity' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Get the donor user.
     */
    public function donor()
    {
        return $this->belongsTo(User::class, 'donor', 'USER_ID');
    }

    /**
     * Scope a query to only include approved blood units.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to only include pending blood units.
     */
    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Scope a query to only include denied blood units.
     */
    public function scopeDenied($query)
    {
        return $query->where('status', -1);
    }

    /**
     * Scope a query to only include available blood units.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 1)
                    ->where('expiration_date', '>', now())
                    ->where('quantity', '>', 0);
    }

    /**
     * Scope a query to only include expired blood units.
     */
    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<', now());
    }

    /**
     * Check if blood unit is available.
     */
    public function isAvailable()
    {
        return $this->status === 1 && 
               $this->expiration_date->isFuture() && 
               $this->quantity > 0;
    }

    /**
     * Check if blood unit is expired.
     */
    public function isExpired()
    {
        return $this->expiration_date->isPast();
    }

    /**
     * Check if blood unit is approved.
     */
    public function isApproved()
    {
        return $this->status === 1;
    }

    /**
     * Get the status text.
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            1 => 'Approved',
            0 => 'Pending',
            -1 => 'Denied',
            default => 'Unknown'
        };
    }

    /**
     * Get the status color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            1 => 'success',
            0 => 'warning',
            -1 => 'danger',
            default => 'secondary'
        };
    }
}
