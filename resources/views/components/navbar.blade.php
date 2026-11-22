<nav class="Navbar">
    <div class="NavbarMarca">
        <span>Tic-Tac-Toe Multijugador</span>
    </div>

    <div class="NavbarUsuario">
        @auth
            @if (Auth::user()->rol === 'administrador')
                <a href="{{ route('admin.dashboard') }}" class="BtnSecundario">Dashboard</a>
                <a href="{{ route('admin.partidas.index') }}" class="BtnSecundario">Partidas</a>
            @endif
            <span>{{ Auth::user()->name }} ({{ Auth::user()->rol }})</span>
            <a href="{{ route('cerrarsesion') }}" class="BtnSecundario">Cerrar sesión</a>
        @endauth
    </div>
</nav>