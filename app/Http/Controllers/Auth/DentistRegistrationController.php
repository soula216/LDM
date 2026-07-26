<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class DentistRegistrationController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prénom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'tél' => 'nullable|string|max:20',
            'gouvernorat' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:1000',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('register')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['role'] = 'dentist';
        $data['requires_approval'] = true;

        $this->userService->createUser($data);

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Votre compte a été créé. Il sera activé après validation par un administrateur.'
            );
    }
}
