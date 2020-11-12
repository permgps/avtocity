<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone',
        'name',
        'password',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function cars()
    {
        return $this->belongsToMany('App\Models\Car');
    }

    public function drivers()
    {
        return $this->belongsToMany('App\Models\User', 'driver_student', 'student_id', 'driver_id');
    }

    public function students()
    {
        return $this->belongsToMany('App\Models\User', 'driver_student', 'driver_id', 'student_id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }

    public function events()
    {
        return $this->hasMany('App\Models\Event', 'student_id');
    }

    public function getPaymentCountAttribute()
    {
        return $this->payments->sum('hours');
    }

    public function getEventCountAttribute()
    {
        return $this->events->count();
    }

    public function getBalanceAttribute()
    {
        return $this->payment_count - $this->event_count;
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
