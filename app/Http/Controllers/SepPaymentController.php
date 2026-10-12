<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SepPaymentController extends Controller
{
    public function show(): View
    {
        return view('payments.sep-test');
    }

    public function callback(Request $request): View
    {
        // SEP callback is not proof of successful payment.
        // Server-side verification will be implemented next.

        return view('payments.sep-result', [
            'callbackReceived' => true,
            'paymentStatus' => 'pending',
        ]);
    }
}
