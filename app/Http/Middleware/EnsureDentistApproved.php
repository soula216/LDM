<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDentistApproved
{
    /**
     * Bloque les dentistes dont le compte n'a pas encore été approuvé par un admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->needsApproval()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Votre compte dentiste est en attente d\'approbation par un administrateur.',
                ]);
        }

        return $next($request);
    }
}
