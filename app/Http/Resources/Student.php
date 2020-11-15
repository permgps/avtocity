<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Student extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $request->user();
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'name' => $this->name,
            'pass' => $user->role == 1 ? $this->pass : '',
            'status' => $this->status,
            'drivers' => $this->drivers,
            'balance' => $this->balance
        ];
    }
}
