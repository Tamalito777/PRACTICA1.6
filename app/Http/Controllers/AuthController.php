<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Repositories\AuthRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function showLoginForm()
    {
        return view('Auth.Login');
    }

    public function login(AuthLoginRequest $request)
    {
        try {
            $credenciales = $request->only('email', 'password');
            $resultado = $this->authRepository->Login($credenciales);

            $request->session()->regenerate();

            return redirect($resultado['redirect'])->with('mensaje', 'Bienvenido al sistema.');
        } catch (Exception $e) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', $e->getMessage());
        }
    }

    public function cerrarSesion(Request $request)
    {
        Session::flush();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('mensaje', 'Sesión cerrada correctamente.');
    }
}