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
            $driver = $user->drivers ? $user->drivers->first() : null;
            if ($driver) {
                $events = Event::where('driver_id',$driver->id)->get();
            } else {
                return [];
            }

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
        $user = $request->user();
        if ($user->role == 4 && $user->id != $event->driver_id) {
            return response()->json([
                'id' => $request['id']
            ],403);
        }
        if ($user->role == 5) {
            if ($user->id != $event->student_id) {
                return response()->json([
                    'id' => $request['id']
                ],403);
            }
            if (strtotime($event->start) - time() < 24*3600) {
                return response()->json([
                    'errors' => [
                        'message' => 'Нельзя отменить запись менее чем за сутки!'
                    ]
                ],422);
            }
        }
        if ($event) {
            $eventService = new Events();
            $eventService->clearStudent($event);
        }
        return response()->json([
            'id' => $request['id']
        ]);
    }

    public function join(Request $request)
    {
        $user = $request->user();
        if ($user->balance <= 0) {
            return response()->json([
                'errors' => [
                    'message' => 'У Вас нет свободных часов для записи! Пополните баланс!'
                ]
            ],422);
        }
        if ($request['id']) {
            $event = Event::find($request['id']);
        } else {
            return response()->json([
                'errors' => [
                    'message' => 'Нет записи!'
                ]
            ],422);
        }
        $event->student_id = $user->id;
        $event->save();

        return response()->json([
            'id' => $event->id
        ]);
    }
}
