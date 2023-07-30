<?php

namespace App\Http\Controllers;

use App\Mail\AppreciationMail;

use App\Models\Contact;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function contactAdminTeam(Request $request){
        $validated = $this->contactValidation($request);

        $contact = Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
        ]);

        if($contact){
            Mail::to($contact->email)->send(new AppreciationMail($contact));
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'fails']);
    }

    protected function contactValidation($request){
        $validated = $request->validate([
            'name' => 'required | min:5',
            'email' => 'required | email',
            'phone' => 'required',
            'message' => 'required'
        ]);

        return $validated;
    }
}
