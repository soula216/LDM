<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

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
                    ->orWhere('message', 'like', '%' . $search . '%');
            });
        }

        $messages = $query
            ->orderByDesc('id')
            ->paginate(self::MESSAGES_PER_PAGE)
            ->withQueryString();

        return view('admin.contact-messages.index', compact('messages'));
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $this->ensureAdmin();

        $contactMessage->delete();

        return redirect()
            ->route('admin.contact-messages.index')
            ->with('success', 'Message supprimé avec succès.');
    }
}
