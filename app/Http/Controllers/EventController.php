<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Resources\Event as EventResource;
use App\Http\Resources\EventCollection;
use App\Services\Events;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function load(Request $request)
    {
        $user = $request->user();
        if ($user->role == 1) {
            $events = Event::get();
        }
        if ($user->role == 4) {
            $events = Event::where('driver_id',$user->id)->get();
        }
        if ($user->role == 5) {
            $events = Event::where('student_id',$user->id)->get();
        }
        return new EventCollection($events);
    }

    public function save(Request $request)
    {
        if ($request['id']) {
            $event = Event::find($request['id']);
        } else {
            $event = new Event();
        }
        $event->start = $request['date'].' '.$request['time'].':00';
        $event->hours = $request['hours'];
        $event->end = date('Y-m-d H:i:s',strtotime($event->start) + $event->hours*2700);
        if ($request['driver']) {
            $event->driver_id = $request['driver'];
        }
        if ($request['student']) {
            $event->student_id = $request['student'];
        }
        $event->save();

        return response()->json([
            'id' => $event->id
        ]);
    }

    public function delete(Request $request)
    {
        $event = Event::find($request['id']);
        if ($event) {
            $event->delete();
        }
        return response()->json([
            'id' => $request['id']
        ]);
    }

    public function clearstudent(Request $request)
    {
        $event = Event::find($request['id']);
        if ($event) {
            $eventService = new Events();
            $eventService->clearStudent($event);
        }
        return response()->json([
            'id' => $request['id']
        ]);
    }
}
