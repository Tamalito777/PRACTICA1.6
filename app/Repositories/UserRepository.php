<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function getAll(): Collection
    {
        return User::orderBy('id', 'desc')->get();
    }

    public function getJugadoresDisponibles(): Collection
    {
        return User::where('rol', 'jugador')->orderBy('name')->get();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): bool
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }
}