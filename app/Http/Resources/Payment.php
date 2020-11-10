<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Payment extends JsonResource
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
            'hours' => $this->hours,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'date' => date('H:i d/m/Y', strtotime($this->created_at)),
        ];
    }
}
