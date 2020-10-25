<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function load(Request $request)
    {
        $students = User::where('role',5)->with('drivers')->get();
        return response()->json([
            'students' => $students
        ]);
    }

    public function save(Request $request)
    {
        if ($request['id']) {
            $student = User::where(['id' => $request['id'], 'role' => 5])->one();
            if ($student) {
                $student->update($request->only('name', 'phone', 'status'));
                $student->drivers()->detach();
                if ($request['driver']) {
                    $student->drivers()->attach($request['driver']);
                }
                if ($request['password']) {
                    $student->password = bcrypt($request['password']);
                    $student->save();
                }
            }
        } else {
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
        }

        return response()->json([
            'id' => $student->id
        ]);
    }

    public function delete(Request $request)
    {
        $student = User::where(['id' => $request['id'], 'role' => 5])->one();
        if ($student) {
            $student->delete();
        }
        return response()->json([
            'id' => $request['id']
        ]);
    }
}
