<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverEditRequest;
use App\Http\Requests\DriverSaveRequest;
use App\Http\Resources\StudentCollection;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function load(Request $request)
    {
        $user = $request->user();
        if ($user->role == 1) {
            $students = User::where('role',5)->with('drivers')->get();
        }
        if ($user->role == 4) {
            $students = User::with(['drivers'])->where('role',5)->get();
            foreach ($students AS $student) {
                if ($student->drivers && $student->drivers[0]->id == $user->id) {

                } else {
                    unset($student);
                }
            }
        }
        if ($user->role == 5) {
            $students = User::where('id',$user->id)->with('drivers')->get();
        }
        return new StudentCollection($students);
    }

    public function save(DriverSaveRequest $request)
    {
        $student = User::create([
            'name' => $request['name'],
            'phone' => $request['phone'],
            'password' => bcrypt($request['password'])
        ]);
        if ($request['driver']) {
            $student->drivers()->attach($request['driver']);
        }
        $student->role = 5;
        $student->save();

        return response()->json([
            'id' => $student->id
        ]);
    }

    public function edit(DriverEditRequest $request)
    {
        $student = User::where(['id' => $request['id'], 'role' => 5])->first();
        if ($student) {
            $student->update($request->only('name', 'status'));
            $student->drivers()->detach();
            if ($request['driver']) {
                $student->drivers()->attach($request['driver']);
            }
            if ($request['password']) {
                $student->password = bcrypt($request['password']);
                $student->save();
            }
            return response()->json([
                'id' => $student->id
            ]);
        }
    }

    public function delete(Request $request)
    {
        $student = User::where(['id' => $request['id'], 'role' => 5])->first();
        if ($student) {
            $student->delete();
        }
        return response()->json([
            'id' => $request['id']
        ]);
    }
}
