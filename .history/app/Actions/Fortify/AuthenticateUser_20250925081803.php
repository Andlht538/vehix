<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticateUser
{
    /**
     * Gérer la tentative de connexion.
     */
    public function __invoke(Request $request)
    {
        // Validation des champs
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Tentative de connexion
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('Les identifiants ne correspondent pas à nos enregistrements.'),
            ]);
        }

        // Vérifier si l’utilisateur est bloqué
        $user = Auth::user();
        if ($user->is_blocked) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => __('Votre compte est bloqué. Contactez l’administrateur.'),
            ]);
        }

        // Regénérer la session
        $request->session()->regenerate();

        return redirect()->intended(config('fortify.home')); // généralement /dashboard
    }
}
