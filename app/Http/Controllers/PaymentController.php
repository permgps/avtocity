<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentCollection;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function load(Request $request)
    {
        $user = $request->user();
        if ($user->role != 1) {
            exit;
        }
        $payments = Payment::with('user')->get();
        return new PaymentCollection($payments);
    }

    public function save(Request $request)
    {
        $user = $request->user();
        if ($user->role != 1) {
            exit;
        }
        $payment = Payment::create([
            'hours' => $request['hours'],
            'user_id' => $request['user']['id']
        ]);
        $payment->save();

        return response()->json([
            'id' => $payment->id
        ]);
    }

    public function delete(Request $request)
    {
        $user = $request->user();
        if ($user->role != 1) {
            exit;
        }
        $payment = Payment::where(['id' => $request['id']])->first();
        if ($payment) {
            $payment->delete();
        }
        return response()->json([
            'id' => $request['id']
        ]);
    }
}
