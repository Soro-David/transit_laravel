<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'contact' => 'required|string|max:50',
            'email' => 'required|email',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Enregistrer dans la base de données
        $contact = Contact::create($validated);

        // Envoyer un email
        Mail::to('contact@aft-app.com')->send(new ContactMessage($contact));

        return back()->with('success', 'Votre message a été envoyé avec succès !');
    }
}
