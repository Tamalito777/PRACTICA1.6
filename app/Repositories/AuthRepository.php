<?php

namespace App\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuthRepository
{
    public function Login(array $credenciales): array
    {
        $user = User::where('email', '=', $credenciales['email'])->first();

        if (!$user) {
            throw new Exception('Usuario no encontrado');
        }

        if (!Auth::validate($credenciales)) {
            throw new Exception('Credenciales incorrectas');
        }

        Auth::login($user);

        if ($user->rol === 'administrador') {
            return [
                'status' => 'success',
                'redirect' => '/admin/dashboard',
            ];
        }

        if ($user->rol === 'jugador') {
            return [
                'status' => 'success',
                'redirect' => '/jugador/lobby',
            ];
        }

        Auth::logout();
        throw new Exception('Rol de usuario no autorizado');
    }

    public function Logout(): void
    {
        Auth::logout();
    }
}