<?php

namespace App\Repositories;

use App\Models\Historial;
use App\Models\Partida;
use App\Models\Tablero;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PartidaRepository
{
    private const COMBINACIONES_GANADORAS = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9],
        [1, 4, 7],
        [2, 5, 8],
        [3, 6, 9],
        [1, 5, 9],
        [3, 5, 7],
    ];

    public function getTodas(): Collection
    {
        return Partida::with(['jugador1', 'jugador2', 'turnoActual', 'ganador', 'tablero'])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getDisponibles(int $userId): Collection
    {
        return Partida::with(['jugador1', 'tablero'])
            ->where('status', 'no_iniciado')
            ->whereNull('jugador_2_id')
            ->where('jugador_1_id', '!=', $userId)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getPartidasDeJugador(int $userId): Collection
    {
        return Partida::with(['jugador1', 'jugador2', 'turnoActual', 'ganador', 'tablero'])
            ->where(function ($query) use ($userId) {
                $query->where('jugador_1_id', $userId)
                    ->orWhere('jugador_2_id', $userId);
            })
            ->orderBy('id', 'desc')
            ->get();
    }

    public function crearPartida(int $jugador1Id): Partida
    {
        return DB::transaction(function () use ($jugador1Id) {
            $partida = Partida::create([
                'jugador_1_id' => $jugador1Id,
                'jugador_2_id' => null,
                'turno_actual_id' => $jugador1Id,
                'ganador_id' => null,
                'status' => 'no_iniciado',
            ]);

            Tablero::create([
                'partida_id' => $partida->id,
            ]);

            return $partida->load(['jugador1', 'tablero']);
        });
    }

    public function unirsePartida(int $partidaId, int $jugador2Id): Partida
    {
        return DB::transaction(function () use ($partidaId, $jugador2Id) {
            $partida = Partida::lockForUpdate()->findOrFail($partidaId);

            if ($partida->status !== 'no_iniciado' || $partida->jugador_2_id !== null) {
                throw new Exception('Esta partida ya no está disponible para unirse.');
            }

            if ((int) $partida->jugador_1_id === $jugador2Id) {
                throw new Exception('No puedes unirte a tu propia partida.');
            }

            $partida->update([
                'jugador_2_id' => $jugador2Id,
                'turno_actual_id' => $partida->jugador_1_id,
                'status' => 'en_juego',
            ]);

            return $partida->load(['jugador1', 'jugador2', 'turnoActual', 'tablero']);
        });
    }

    public function realizarMovimiento(int $partidaId, int $userId, int $casilla): Partida
    {
        return DB::transaction(function () use ($partidaId, $userId, $casilla) {
            $partida = Partida::lockForUpdate()->findOrFail($partidaId);
            $tablero = Tablero::where('partida_id', $partida->id)->lockForUpdate()->firstOrFail();

            if ($partida->status !== 'en_juego') {
                throw new Exception('La partida no está en juego.');
            }

            $esJugador1 = (int) $partida->jugador_1_id === $userId;
            $esJugador2 = (int) $partida->jugador_2_id === $userId;

            if (!$esJugador1 && !$esJugador2) {
                throw new Exception('No formas parte de esta partida.');
            }

            if ((int) $partida->turno_actual_id !== $userId) {
                throw new Exception('No es tu turno para realizar un movimiento.');
            }

            $campoCasilla = 'casilla_' . $casilla;

            if ($tablero->{$campoCasilla} !== null) {
                throw new Exception('La casilla seleccionada ya está ocupada.');
            }

            $simbolo = $esJugador1 ? 'X' : 'O';
            $tablero->update([
                $campoCasilla => $simbolo,
            ]);

            $tableroRefrescado = $tablero->fresh();

            if ($this->hayGanador($tableroRefrescado, $simbolo)) {
                $perdedorId = $esJugador1 ? (int) $partida->jugador_2_id : (int) $partida->jugador_1_id;

                $partida->update([
                    'ganador_id' => $userId,
                    'turno_actual_id' => null,
                    'status' => 'finalizado',
                ]);

                $this->registrarHistorial($partida->id, $userId, $perdedorId, 'victoria');
            } elseif ($this->tableroLleno($tableroRefrescado)) {
                $partida->update([
                    'ganador_id' => null,
                    'turno_actual_id' => null,
                    'status' => 'finalizado',
                ]);

                $this->registrarHistorial(
                    $partida->id,
                    (int) $partida->jugador_1_id,
                    (int) $partida->jugador_2_id,
                    'empate'
                );
            } else {
                $siguienteTurnoId = $esJugador1 ? (int) $partida->jugador_2_id : (int) $partida->jugador_1_id;

                $partida->update([
                    'turno_actual_id' => $siguienteTurnoId,
                ]);
            }

            return $partida->load(['jugador1', 'jugador2', 'turnoActual', 'ganador', 'tablero']);
        });
    }

    public function abandonarPartida(int $partidaId, int $userId): Partida
    {
        return DB::transaction(function () use ($partidaId, $userId) {
            $partida = Partida::lockForUpdate()->findOrFail($partidaId);

            if ($partida->status === 'finalizado') {
                throw new Exception('La partida ya ha finalizado.');
            }

            $esJugador1 = (int) $partida->jugador_1_id === $userId;
            $esJugador2 = (int) $partida->jugador_2_id === $userId;

            if (!$esJugador1 && !$esJugador2) {
                throw new Exception('No tienes permiso para abandonar esta partida.');
            }

            $ganadorId = null;

            if ($partida->jugador_2_id !== null) {
                $ganadorId = $esJugador1 ? (int) $partida->jugador_2_id : (int) $partida->jugador_1_id;
                $this->registrarHistorial($partida->id, $ganadorId, $userId, 'victoria');
            }

            $partida->update([
                'ganador_id' => $ganadorId,
                'turno_actual_id' => null,
                'status' => 'finalizado',
            ]);

            return $partida->load(['jugador1', 'jugador2', 'turnoActual', 'ganador', 'tablero']);
        });
    }

    public function cancelarPartidaAdmin(int $partidaId): Partida
    {
        return DB::transaction(function () use ($partidaId) {
            $partida = Partida::lockForUpdate()->findOrFail($partidaId);

            if ($partida->status === 'finalizado') {
                throw new Exception('La partida ya está finalizada.');
            }

            $partida->update([
                'turno_actual_id' => null,
                'status' => 'finalizado',
            ]);

            return $partida->load(['jugador1', 'jugador2', 'turnoActual', 'ganador', 'tablero']);
        });
    }

    public function getHistorialGeneral(): Collection
    {
        return Historial::with(['partida.jugador1', 'partida.jugador2', 'user'])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getHistorialJugador(int $userId): Collection
    {
        return Historial::with(['partida.jugador1', 'partida.jugador2', 'user'])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();
    }

    private function hayGanador(Tablero $tablero, string $simbolo): bool
    {
        foreach (self::COMBINACIONES_GANADORAS as $combinacion) {
            [$a, $b, $c] = $combinacion;
            if (
                $tablero->{'casilla_' . $a} === $simbolo &&
                $tablero->{'casilla_' . $b} === $simbolo &&
                $tablero->{'casilla_' . $c} === $simbolo
            ) {
                return true;
            }
        }

        return false;
    }

    private function tableroLleno(Tablero $tablero): bool
    {
        for ($i = 1; $i <= 9; $i++) {
            if ($tablero->{'casilla_' . $i} === null) {
                return false;
            }
        }

        return true;
    }

    private function registrarHistorial(int $partidaId, int $jugadorA, ?int $jugadorB, string $resultado): void
    {
        if ($resultado === 'empate') {
            Historial::create([
                'partida_id' => $partidaId,
                'user_id' => $jugadorA,
                'status' => 'empate',
            ]);

            if ($jugadorB !== null) {
                Historial::create([
                    'partida_id' => $partidaId,
                    'user_id' => $jugadorB,
                    'status' => 'empate',
                ]);
            }

            return;
        }

        Historial::create([
            'partida_id' => $partidaId,
            'user_id' => $jugadorA,
            'status' => 'ganada',
        ]);

        if ($jugadorB !== null) {
            Historial::create([
                'partida_id' => $partidaId,
                'user_id' => $jugadorB,
                'status' => 'perdida',
            ]);
        }
    }

    public function getHistorialGeneralFiltrado(array $filters): Collection
{
    $query = Historial::with(['partida.jugador1', 'partida.jugador2', 'user'])
        ->orderBy('id', 'desc');

    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }

    if (!empty($filters['usuario'])) {
        $nombreUsuario = $filters['usuario'];
        $query->whereHas('user', function ($q) use ($nombreUsuario) {
            $q->where('name', 'like', '%' . $nombreUsuario . '%');
        });
    }

    return $query->get();
}

public function getHistorialJugadorFiltrado(int $userId, array $filters): Collection
{
    $query = Historial::with(['partida.jugador1', 'partida.jugador2', 'user'])
        ->where('user_id', $userId)
        ->orderBy('id', 'desc');

    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }

    if (!empty($filters['partida_id'])) {
        $query->where('partida_id', (int) $filters['partida_id']);
    }

    return $query->get();
}

public function getPartidasFiltradas(array $filters): Collection
{
    $query = Partida::with(['jugador1', 'jugador2', 'turnoActual', 'ganador'])
        ->orderBy('id', 'desc');

    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }

    if (!empty($filters['jugador'])) {
        $nombreJugador = $filters['jugador'];
        $query->where(function ($q) use ($nombreJugador) {
            $q->whereHas('jugador1', function ($sub) use ($nombreJugador) {
                $sub->where('name', 'like', '%' . $nombreJugador . '%');
            })->orWhereHas('jugador2', function ($sub) use ($nombreJugador) {
                $sub->where('name', 'like', '%' . $nombreJugador . '%');
            });
        });
    }

    return $query->get();
}
}