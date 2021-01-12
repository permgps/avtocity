<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverEditRequest;
use App\Http\Requests\DriverSaveRequest;
use App\Models\Template;
use App\Models\User;
use App\Services\Events;
use App\Services\Helper;
use Illuminate\Http\Request;

class DriverController extends Controller
{

    public function load(Request $request)
    {
        $user = $request->user();
        if ($user->role == 1) {
            $drivers = User::where('role',4)->with('cars')->get();
        }
        if ($user->role == 4) {
            $drivers = User::where('id',$user->id)->with('cars')->get();
        }
        if ($user->role == 5) {
            $drivers = $user->drivers;
        }

        return response()->json([
            'drivers' => $drivers
        ]);
    }

    public function save(DriverSaveRequest $request)
    {
        $driver = User::create([
            'name' => $request['name'],
            'phone' => $request['phone'],
            'password' => bcrypt($request['password']),
            'pass' => $request['password']
        ]);
        if ($request['car_id']) {
            $driver->cars()->attach($request['car_id']);
        }
        $driver->role = 4;
        $driver->save();

        return response()->json([
            'id' => $driver->id
        ]);
    }

    public function edit(DriverEditRequest $request)
    {
        $driver = User::find($request['id']);
        $driver->update($request->only('name', 'status', 'phone'));
        $driver->cars()->detach();
        if ($request['car_id']) {
            $driver->cars()->attach($request['car_id']);
        }
        if ($request['password']) {
            $driver->password = bcrypt($request['password']);
            $driver->pass = $request['password'];
            $driver->save();
        }
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
        $user = $request->user();
        $driver_id = null;
        if ($user->role == 1) {

        }
        if ($user->role == 4) {
            $driver_id = $user->id;
        }
        if ($user->role == 5) {
            exit;
        }
        if ($request['tab'] == 'period') {
            $events = $eventService->getEvents($request['period']['start'],$request['period']['end'],$driver_id);
            $razn = strtotime(Helper::getDT($request['period']['to'])) - strtotime(Helper::getDT($request['period']['start']));
            foreach ($events as $event) {
                $new_event = $event->replicate();
                $new_event->start = date('Y-m-d H:i:s', strtotime($new_event->start) + $razn);
                $new_event->end = date('Y-m-d H:i:s', strtotime($new_event->end) + $razn);
                $new_event->driver_id = $request['driver'];
                $new_event->student_id = null;
                $new_event->save();
                $res[] = $new_event;
            }
            return response()->json([
                'events' => $res
            ]);
        } elseif ($request['tab'] == 'template') {
            $template = Template::find($request['template']['template']['id']);
            $events = $template->createEvents(Helper::getDT($request['template']['start']),Helper::getDT($request['template']['end']),$request['driver']);
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
            $events = $eventService->getEvents($date,$date,$driver_id);
            foreach ($events as $event) {
                $new_event = $event->replicate();
                $new_event->start = date('Y-m-d H:i:s', strtotime($new_event->start) + $razn);
                $new_event->end = date('Y-m-d H:i:s', strtotime($new_event->end) + $razn);
                $new_event->driver_id = $request['driver'];
                $new_event->student_id = null;
                $new_event->save();
                $res[] = $new_event;
            }
            return response()->json([
                'events' => $res
            ]);
        }
    }

    public function report(Request $request)
    {
        $user = $request->user();
        if ($user->role == 1) {
            $eventService = new Events();
            $events = $eventService->getEvents($request['from'],$request['to']);
            $drivers = User::where('role',4)->with('cars')->get()->map(function ($driver, $key) use ($events) {
                $driver->all = count($events->filter(function ($event, $key) use ($driver)  {
                    return $event->driver_id == $driver->id;
                }));
                $driver->feel = count($events->filter(function ($event, $key) use ($driver)  {
                    return $event->driver_id == $driver->id && $event->student_id !== null;
                }));
                return $driver;
            });
            return response()->json([
                'drivers' => $drivers
            ]);
        } else {
            return response(null,401);
        }
    }
}
