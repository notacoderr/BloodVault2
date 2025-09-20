<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blood_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'blood_type',
        'units_needed',
        'urgency',
        'reason',
        'hospital',
        'contact_person',
        'contact_number',
        'request_date',
        'status',
        'admin_notes',
        'blood_available',
        'allocated_units',
        'additional_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'request_date' => 'date',
        'blood_available' => 'boolean',
        'units_needed' => 'integer',
        'allocated_units' => 'integer',
    ];

    /**
     * Get the user that owns the blood request.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'USER_ID');
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include critical urgency requests.
     */
    public function scopeCritical($query)
    {
        return $query->where('urgency', 'critical');
    }

    /**
     * Scope a query to only include high urgency requests.
     */
    public function scopeHigh($query)
    {
        return $query->where('urgency', 'high');
    }

    /**
     * Get the urgency level with color coding.
     */
    public function getUrgencyColorAttribute()
    {
        return match($this->urgency) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Get the status with color coding.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'completed' => 'success',
            'cancelled' => 'secondary',
            default => 'secondary'
        };
    }
}
