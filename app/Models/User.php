<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'USER_ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'name',
        'dob',
        'sex',
        'address',
        'city',
        'province',
        'contact',
        'bloodtype',
        'usertype',
        'schedule_date',
        'last_donation_date',
        'email_verification_token',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'schedule_date' => 'date',
        'last_donation_date' => 'date',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'USER_ID';
    }

    /**
     * Get the blood requests for the user.
     */
    public function bloodRequests()
    {
        return $this->hasMany(BloodRequest::class, 'user_id', 'USER_ID');
    }

    /**
     * Get the blood donations for the user.
     */
    public function bloodDonations()
    {
        return $this->hasMany(BloodDonation::class, 'user_id', 'USER_ID');
    }

    /**
     * Get the appointments for the user.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id', 'USER_ID');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->usertype === 'admin';
    }

    /**
     * Check if user is donor.
     */
    public function isDonor()
    {
        return $this->usertype === 'donor';
    }

    /**
     * Check if user is requester.
     */
    public function isRequester()
    {
        return $this->usertype === 'requester';
    }

    /**
     * Check if user's email is verified.
     */
    public function isEmailVerified()
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Generate email verification token.
     */
    public function generateEmailVerificationToken()
    {
        $this->email_verification_token = \Str::random(64);
        $this->save();
        return $this->email_verification_token;
    }

    /**
     * Mark email as verified.
     */
    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();
        $this->is_verified = true;
        $this->email_verification_token = null;
        $this->save();
    }

}
