<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Event extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => 'Событие',
            'details' => 'Time to pitch',
            'date' => date('Y-m-d', strtotime($this->start)),
            'time' => date('H:i', strtotime($this->start)),
            'endtime' => date('H:i', strtotime($this->end)),
            'hours' => $this->hours,
            'duration' => (strtotime($this->end) - strtotime($this->start))/60,
            'bgcolor' => 'red',
            'icon' => 'alarm',
            'driver_id' => $this->driver_id,
            'student_id' => $this->student_id,
            'success' => strtotime($this->end) < time()
        ];
    }
}
