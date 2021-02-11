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
            if (!$event) {
                return response()->json([
                    'id' => null,
                    'message' => 'Нет такой записи!'
                ]);
            }
        } else {
            $event = new Event();
        }
        $event->start = $request['date'].' '.$request['time'].':00';
        $event->hours = $request['hours'];
        $event->end = date('Y-m-d H:i:s',strtotime($event->start) + $event->hours*5400);
        if ($request['driver']) {
            $event->driver_id = $request['driver'];
        }
        if ($request['student']) {
            if ($event->student_id) {
                return response()->json([
                    'id' => null,
                    'message' => 'Запись занята!'
                ]);
            }
            $event->student_id = $request['student'];
        }
        $event->save();

        return response()->json([
            'id' => $event->id
        ]);
    }

    public function delete(Request $request)
    {
        $period = $request['period'];
        $event = Event::find($request['id']);
        if ($event) {
            switch ($period) {
                case 'item':
                    $event->delete();
                    break;

                case 'day':
                    $events = Event::where('start', '>=', date('Y-m-d 00:00:00',strtotime($event->start)))
                        ->where('start', '<=', date('Y-m-d 23:59:59',strtotime($event->start)))
                        ->get();
                    foreach ($events as $ev) {
                        $ev->delete();
                    }
                    break;

                case 'week':
                    $start = date("Y-m-d", strtotime('monday this week', strtotime($event->start)));
                    $end = date("Y-m-d", strtotime('saturday this week', strtotime($event->start)) + 24*3600);
                    $events = Event::where('start', '>=', date('Y-m-d 00:00:00',strtotime($start)))
                        ->where('start', '<=', date('Y-m-d 23:59:59',strtotime($end)))
                        ->get();
                    foreach ($events as $ev) {
                        $ev->delete();
                    }
                    break;

                case 'month':
                    $start = date("Y-m-01", strtotime($event->start));
                    $end = date("Y-m-31", strtotime($event->start));
                    $events = Event::where('start', '>=', date('Y-m-d 00:00:00',strtotime($start)))
                        ->where('start', '<=', date('Y-m-d 23:59:59',strtotime($end)))
                        ->get();
                    foreach ($events as $ev) {
                        $ev->delete();
                    }
                    break;
            }
            return response()->json([
                'id' => $request['id']
            ]);
        }
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
                    'message' => 'У Вас нет свободных занятий для записи! Пополните баланс!'
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
