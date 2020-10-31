<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Events
{
    public function getEvents($start,$end,$drivers = null)
    {
        return Event::where([
            ['start', '>=', $start.' 00:00:00'],
            ['end', '<=', $end.' 23:59:59']
        ])->get();
    }

    public function clearStudent(Event $event)
    {
        $event->student_id = null;
        $event->save();
    }
}

