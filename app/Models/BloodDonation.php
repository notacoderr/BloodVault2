<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodDonation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blood_donations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'donor_name',
        'donor_email',
        'blood_type',
        'donation_date',
        'quantity',
        'screening_status',
        'status',
        'screening_answers',
        'notes',
        'admin_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'donation_date' => 'datetime',
        'quantity' => 'integer',
    ];

    /**
     * Get the user that owns the blood donation.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'USER_ID');
    }

    /**
     * Scope a query to only include pending donations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved donations.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include completed donations.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include rejected donations.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Get the status with color coding.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'completed' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Check if donation is eligible for processing.
     */
    public function isEligible()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if donation is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if user can donate based on cooldown period.
     * Standard cooldown is 56 days (8 weeks) from last completed donation.
     */
    public static function canUserDonate($userId)
    {
        $lastCompletedDonation = self::where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('donation_date', 'desc')
            ->first();

        if (!$lastCompletedDonation) {
            return true; // First time donor
        }

        $cooldownPeriod = 56; // days
        $nextEligibleDate = $lastCompletedDonation->donation_date->addDays($cooldownPeriod);
        
        return now()->gte($nextEligibleDate);
    }

    /**
     * Get the next eligible donation date for a user.
     */
    public static function getNextEligibleDate($userId)
    {
        $lastCompletedDonation = self::where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('donation_date', 'desc')
            ->first();

        if (!$lastCompletedDonation) {
            return null; // First time donor
        }

        $cooldownPeriod = 56; // days
        return $lastCompletedDonation->donation_date->addDays($cooldownPeriod);
    }

    /**
     * Get the remaining cooldown days for a user.
     */
    public static function getRemainingCooldownDays($userId)
    {
        $nextEligibleDate = self::getNextEligibleDate($userId);
        
        if (!$nextEligibleDate) {
            return 0; // Can donate now
        }

        $remainingDays = now()->diffInDays($nextEligibleDate, false);
        return max(0, $remainingDays);
    }
}
