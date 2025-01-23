<?php

namespace App\Http\Controllers;

use App\Mail\NotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $data = [
            'name' => $request->input('name'),
            'message' => $request->input('message'),
        ];

        Mail::to($request->input('email'))->send(new NotificationMail($data));

        return response()->json(['message' => 'E-mail envoyé avec succès!']);
    }
}