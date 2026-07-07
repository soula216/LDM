<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactMessageController extends Controller
{
    private const MESSAGES_PER_PAGE = 20;

    private function ensureAdmin(): void
    {
        if (! auth()->user()?->hasRole('admin')) {
            abort(404);
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $query = ContactMessage::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('message', 'like', '%' . $search . '%')
                    ->orWhere('attachment_name', 'like', '%' . $search . '%');
            });
        }

        $messages = $query
            ->orderByDesc('id')
            ->paginate(self::MESSAGES_PER_PAGE)
            ->withQueryString();

        return view('admin.contact-messages.index', compact('messages'));
    }

    public function downloadAttachment(ContactMessage $contactMessage): StreamedResponse
    {
        $this->ensureAdmin();

        if (! $contactMessage->hasAttachment()
            || ! Storage::disk('public')->exists($contactMessage->attachment_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $contactMessage->attachment_path,
            $contactMessage->attachment_name ?: basename($contactMessage->attachment_path)
        );
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $this->ensureAdmin();

        if ($contactMessage->hasAttachment()) {
            Storage::disk('public')->delete($contactMessage->attachment_path);
        }

        $contactMessage->delete();

        return redirect()
            ->route('admin.contact-messages.index')
            ->with('success', 'Message supprimé avec succès.');
    }
}
