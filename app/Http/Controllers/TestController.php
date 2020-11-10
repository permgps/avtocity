<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function test()
    {
        $user = User::find(2);
        $students = User::with(['drivers'])->where('role',5)->get();
        foreach ($students AS $student) {
            if ($student->drivers && $student->drivers[0]->id == $user->id) {

            } else {
                unset($student);
            }
        }
        var_dump(count(new StudentCollection($students)));
        DB::listen(function($query) {
            var_dump($query->sql, $query->bindings);
        });
    }
}
