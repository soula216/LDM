<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('vitrine')
                ->withFragment('contact')
                ->withErrors($validator)
                ->withInput();
        }

        ContactMessage::create($validator->validated());

        return redirect()
            ->route('vitrine')
            ->withFragment('contact')
            ->with('contact_success', 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.');
    }
}
