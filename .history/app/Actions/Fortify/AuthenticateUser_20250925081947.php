<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class AuthenticateUser
{
      public function __invoke(Request $request)
    {
        $request->validate([
            Fortify::username() => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where(Fortify::username(), $request->{Fortify::username()})->first();

        if (!$user) {
            throw ValidationException::withMessages([
                Fortify::username() => [trans('auth.failed')],
            ]);
        }

        // Vérifier si l'utilisateur est bloqué
        if ($user->isBlocked()) {
            throw ValidationException::withMessages([
                Fortify::username() => ['Votre compte est temporairement bloqué. Réessayez plus tard.'],
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            // Incrémenter les tentatives de connexion
            $user->increment('login_attempts');

            // Bloquer après 3 tentatives
            if ($user->login_attempts >= 3) {
                $user->update([
                    'blocked_until' => now()->addMinutes(30), // Bloqué 30 minutes
                ]);

                throw ValidationException::withMessages([
                    Fortify::username() => ['Trop de tentatives de connexion. Compte bloqué pour 30 minutes.'],
                ]);
            }

            throw ValidationException::withMessages([
                Fortify::username() => [trans('auth.failed')],
            ]);
        }

        // Réinitialiser les tentatives en cas de connexion réussie
        $user->update([
            'login_attempts' => 0,
            'blocked_until' => null,
        ]);

        return $user;
    }
}
