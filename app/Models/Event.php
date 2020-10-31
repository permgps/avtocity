<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start',
        'end',
        'hours',
        'driver_id',
        'student_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function student()
    {
        return $this->hasOne('App\Models\User', 'student_id');
    }

    public function driver()
    {
        return $this->hasOne('App\Models\User', 'driver_id');
    }
}
