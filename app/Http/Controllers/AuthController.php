<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function signin(Request $request)
    {
        if (!$token = auth()->attempt([
            'phone' => $request['phone'],
            'password' => $request['password'],
            'status' => 1,
        ]))
        {
            return response(null,401);
        }

        return response()->json(compact('token'));
    }

    public function logout(Request $request)
    {
        auth()->logout();
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'role' => $user->role,
            'phone' => $user->phone,
            'name' => $user->name,
        ]);
    }
}
