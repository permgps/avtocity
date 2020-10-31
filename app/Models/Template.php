<?php

namespace App\Models;

use App\Services\Helper;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'opts',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $casts = [
        'id' => 'int',
        'opts' => 'array'
    ];

    public function createEvents($start,$end,$driver_id = null)
    {
        $events = [];
        $days = Helper::getDaysArr($start,$end);
        foreach ($days as $day) {
            $number_day = date('w', strtotime($day));
            $date = date('Y-m-d',strtotime($day));
            foreach ($this->opts as $templ) {
                if (in_array($number_day,$templ['days'])) {
                    foreach ($templ['hours'] as $hour) {
                        $events[] = Event::create([
                            'start' => $date.' '.$hour['start'],
                            'end' => date('Y-m-d H:i:s',strtotime($date.' '.$hour['start']) + $hour['hours']*2700),
                            'hours' => $hour['hours'],
                            'driver_id' => $driver_id
                        ]);
                    }
                }
            }
        }
        return $events;
    }
}
