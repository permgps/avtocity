<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\User;
use App\Services\Events;
use App\Services\Helper;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function load(Request $request)
    {
        $drivers = User::where('role',4)->with('cars')->get();
        return response()->json([
            'drivers' => $drivers
        ]);
    }

    public function save(Request $request)
    {
        if ($request['id']) {
            $driver = User::find($request['id']);
            $driver->update($request->only('name', 'phone', 'status'));
            $driver->cars()->detach();
            if ($request['car_id']) {
                $driver->cars()->attach($request['car_id']);
            }
            if ($request['password']) {
                $driver->password = bcrypt($request['password']);
                $driver->save();
            }
        } else {
            $driver = User::create([
                'name' => $request['name'],
                'phone' => $request['phone'],
                'password' => bcrypt($request['password'])
            ]);
            if ($request['car_id']) {
                $driver->cars()->attach($request['car_id']);
            }
            $driver->role = 4;
            $driver->save();
        }

        return response()->json([
            'id' => $driver->id
        ]);
    }

    public function delete(Request $request)
    {
        $driver = User::find($request['id']);
        if ($driver) {
            $driver->delete();
        }
        return response()->json([
            'id' => $request['id']
        ]);
    }

    public function feel(Request $request)
    {
        $res = [];
        $eventService = new Events();
        if ($request['tab'] == 'period') {
            $events = $eventService->getEvents($request['period']['start'],$request['period']['end']);
            $razn = strtotime(Helper::getDT($request['period']['to'])) - strtotime(Helper::getDT($request['period']['start']));
            foreach ($events as $event) {
                $new_event = $event->replicate();
                $new_event->start = date('Y-m-d H:i:s', strtotime($new_event->start) + $razn);
                $new_event->end = date('Y-m-d H:i:s', strtotime($new_event->end) + $razn);
                $new_event->driver_id = $request['driver']['id'];
                $new_event->student_id = null;
                $new_event->save();
                $res[] = $new_event;
            }
            return response()->json([
                'events' => $res
            ]);
        } elseif ($request['tab'] == 'template') {
            $template = Template::find($request['template']['template']['id']);
            $events = $template->createEvents(Helper::getDT($request['template']['start']),Helper::getDT($request['template']['end']),$request['driver']['id']);
            return response()->json([
                'events' => $events
            ]);
        } elseif ($request['tab'] == 'copy') {
            if ($request['copy']['prev'] == 'week') {
                $date = date('Y-m-d', strtotime($request['copy']['curdate']) - 7*24*3600);
                $razn = 7*24*3600;
            } else {
                $date = date('Y-m-d', strtotime($request['copy']['curdate']) - 24*3600);
                $razn = 24*3600;
            }
            $events = $eventService->getEvents($date,$date);
            foreach ($events as $event) {
                $new_event = $event->replicate();
                $new_event->start = date('Y-m-d H:i:s', strtotime($new_event->start) + $razn);
                $new_event->end = date('Y-m-d H:i:s', strtotime($new_event->end) + $razn);
                $new_event->driver_id = $request['driver']['id'];
                $new_event->student_id = null;
                $new_event->save();
                $res[] = $new_event;
            }
            return response()->json([
                'events' => $res
            ]);
        }
    }
}
