<?php

namespace App\Http\Controllers;

use App\Models\User;
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
}
