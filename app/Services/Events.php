<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Events
{
    public function getEvents($start,$end,$driver_id = null)
    {
        $where = [
            ['start', '>=', $start.' 00:00:00'],
            ['end', '<=', $end.' 23:59:59']
        ];
        if ($driver_id) {
            $where[] = ['driver_id', '=', $driver_id];
        }
        return Event::where($where)->get();
    }

    public function clearStudent(Event $event)
    {
        $event->student_id = null;
        $event->save();
    }
}

