<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Repositories\PartidaRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $userRepository;
    protected $partidaRepository;

    public function __construct(UserRepository $userRepository, PartidaRepository $partidaRepository)
    {
        $this->userRepository = $userRepository;
        $this->partidaRepository = $partidaRepository;
    }

    public function dashboard(Request $request){
    $filters = $request->only(['status', 'usuario']);
    $usuarios = $this->userRepository->getAll();
    $partidas = $this->partidaRepository->getTodas();
    $historial = $this->partidaRepository->getHistorialGeneralFiltrado($filters);

    return view('Admin.Dashboard', compact('usuarios', 'partidas', 'historial'));
    }

    public function storeUser(StoreUserRequest $request)
    {
        try {
            $this->userRepository->create($request->validated());

            return back()->with('mensaje', 'Usuario creado correctamente.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'No se pudo crear el usuario: ' . $e->getMessage());
        }
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'rol' => 'required|in:administrador,jugador',
        ]);

        try {
            $this->userRepository->update($user, $validated);

            return back()->with('mensaje', 'Usuario actualizado correctamente.');
        } catch (Exception $e) {
            return back()->with('error', 'No se pudo actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function destroyUser(User $user)
    {
        try {
            $this->userRepository->delete($user);

            return back()->with('mensaje', 'Usuario eliminado correctamente.');
        } catch (Exception $e) {
            return back()->with('error', 'No se pudo eliminar el usuario: ' . $e->getMessage());
        }
    }

    public function cancelarPartida(int $id)
    {
        try {
            $this->partidaRepository->cancelarPartidaAdmin($id);

            return back()->with('mensaje', 'Partida cancelada correctamente.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

  public function partidas(Request $request)
{
    $filters = $request->only(['status', 'jugador']);
    $partidas = $this->partidaRepository->getPartidasFiltradas($filters);

    return view('Admin.Partidas', compact('partidas'));
}
}