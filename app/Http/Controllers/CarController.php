<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function load(Request $request)
    {
        $cars = Car::all();
        return response()->json([
            'cars' => $cars
        ]);
    }

    public function save(Request $request)
    {
        if ($request['id']) {
            $car = Car::find($request['id']);
            $car->update($request->all());
        } else {
            $car = Car::create([
                'marka' => $request['marka'],
                'category' => $request['category'],
                'kpp' => $request['kpp'],
                'nomer' => $request['nomer'],
                'year' => $request['year'],
            ]);
        }

        return response()->json([
            'id' => $car->id
        ]);
    }

    public function delete(Request $request)
    {
        $car = Car::find($request['id']);
        if ($car) {
            $car->delete();
        }
        return response()->json([
            'id' => $request['id']
        ]);
    }
}
