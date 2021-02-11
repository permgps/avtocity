<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Event extends Model
{
    use SoftDeletes;
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

    public function save(array $options = [])
    {
        // before save code
        Log::debug('save event',[
            'options' => $options
        ]);
        $events_count = Event::where([
            ['id', '<>', $this->id],
            ['driver_id', '=', $this->driver_id],
            ['start', '<', $this->end],
            ['end', '>', $this->start]
        ])->count();
        if ($events_count) {
            return false;
        }
        parent::save($options);
        // after save code
    }
}
