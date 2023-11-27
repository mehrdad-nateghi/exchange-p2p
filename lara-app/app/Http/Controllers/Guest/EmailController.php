<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Mail\TestMailHog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendTestEmail()
    {
        $email = new TestMailHog();
        Log::alert("hereee.");

        Mail::to('mahdijafari3957@gmail.com')->send($email);
        Log::alert("hereee 2222.");

        return "Email sent successfully!";
    }
}
