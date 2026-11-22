<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovimientoRequest;
use App\Models\Partida;
use App\Repositories\PartidaRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartidaController extends Controller
{
    protected $partidaRepository;
    protected $userRepository;

    public function __construct(PartidaRepository $partidaRepository, UserRepository $userRepository)
    {
        $this->partidaRepository = $partidaRepository;
        $this->userRepository = $userRepository;
    }

    public function lobby(Request $request)
    {
    $userId = Auth::id();
    $filters = $request->only(['status', 'partida_id']);
    $jugadores = $this->userRepository->getJugadoresDisponibles();
    $disponibles = $this->partidaRepository->getDisponibles($userId);
    $misPartidas = $this->partidaRepository->getPartidasDeJugador($userId);
    $historial = $this->partidaRepository->getHistorialJugadorFiltrado($userId, $filters);

    return view('Jugador.Lobby', compact('jugadores', 'disponibles', 'misPartidas', 'historial'));
    }


    public function crear()
    {
        try {
            $partida = $this->partidaRepository->crearPartida(Auth::id());

            return redirect()
                ->route('jugador.partidas.show', $partida->id)
                ->with('mensaje', 'Partida creada. Esperando que otro jugador se una.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function unirse(int $id)
    {
        try {
            $partida = $this->partidaRepository->unirsePartida($id, Auth::id());

            return redirect()
                ->route('jugador.partidas.show', $partida->id)
                ->with('mensaje', 'Te has unido a la partida. ¡A jugar!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $partida = Partida::with(['jugador1', 'jugador2', 'turnoActual', 'ganador', 'tablero'])
            ->findOrFail($id);

        $userId = Auth::id();
        $esParticipante = (int) $partida->jugador_1_id === $userId || (int) $partida->jugador_2_id === $userId;
        $esAdmin = Auth::user()->rol === 'administrador';

        if (!$esParticipante && !$esAdmin) {
            abort(403, 'No tienes autorización para ver esta partida.');
        }

        $simboloUsuario = (int) $partida->jugador_1_id === $userId
            ? 'X'
            : ((int) $partida->jugador_2_id === $userId ? 'O' : null);

        $esMiTurno = $partida->status === 'en_juego' && (int) $partida->turno_actual_id === $userId;

        return view('Jugador.Partida', compact('partida', 'simboloUsuario', 'esMiTurno'));
    }

    public function estado(int $id)
    {
        $partida = Partida::with(['jugador1', 'jugador2', 'turnoActual', 'ganador', 'tablero'])
            ->findOrFail($id);

        $userId = Auth::id();
        $esParticipante = (int) $partida->jugador_1_id === $userId || (int) $partida->jugador_2_id === $userId;
        $esAdmin = Auth::user()->rol === 'administrador';

        if (!$esParticipante && !$esAdmin) {
            abort(403, 'No tienes autorización para consultar esta partida.');
        }

        return response()->json([
            'id' => $partida->id,
            'status' => $partida->status,
            'turno_actual_id' => $partida->turno_actual_id,
            'turno_actual_nombre' => $partida->turnoActual->name ?? null,
            'ganador_id' => $partida->ganador_id,
            'ganador_nombre' => $partida->ganador->name ?? null,
            'jugador_2_id' => $partida->jugador_2_id,
            'jugador_2_nombre' => $partida->jugador2->name ?? null,
            'tablero' => $partida->tablero?->only([
                'casilla_1', 'casilla_2', 'casilla_3',
                'casilla_4', 'casilla_5', 'casilla_6',
                'casilla_7', 'casilla_8', 'casilla_9',
            ]),
        ]);
    }

    public function mover(MovimientoRequest $request, int $id)
    {
        try {
            $this->partidaRepository->realizarMovimiento($id, Auth::id(), (int) $request->validated()['casilla']);

            return redirect()
                ->route('jugador.partidas.show', $id)
                ->with('mensaje', 'Movimiento registrado.');
        } catch (Exception $e) {
            return redirect()
                ->route('jugador.partidas.show', $id)
                ->with('error', $e->getMessage());
        }
    }

    public function abandonar(Request $request, int $id)
    {
        try {
            $this->partidaRepository->abandonarPartida($id, Auth::id());

            return redirect()
                ->route('jugador.lobby')
                ->with('mensaje', 'Has abandonado la partida.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    
}