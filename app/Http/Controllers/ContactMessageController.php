<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormSubmitted;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            'attachment' => 'nullable|file|extensions:pdf,jpg,jpeg,png,webp,doc,docx,stl|max:46080',
        ], [
            'attachment.extensions' => 'La pièce jointe doit être un fichier PDF, image, Word ou STL.',
            'attachment.max' => 'La pièce jointe ne doit pas dépasser 45 Mo.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('vitrine')
                ->withFragment('contact')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        unset($data['attachment']);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $filename = 'contact_' . time() . '_' . uniqid() . '.' . $extension;

            $data['attachment_path'] = $file->storeAs('contact-messages', $filename, 'public');
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        $contactMessage = ContactMessage::create($data);

        try {
            Mail::to(config('contact.notification_email'))
                ->send(new ContactFormSubmitted($contactMessage));
        } catch (\Throwable $e) {
            Log::error('Échec envoi email formulaire contact', [
                'contact_message_id' => $contactMessage->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('vitrine')
            ->withFragment('contact')
            ->with('contact_success', 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.');
    }
}
