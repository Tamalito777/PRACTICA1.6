<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala de Juego - Tic-Tac-Toe</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <x-navbar />

    <div class="TituloPagina">
        <h1>Sala de Juego</h1>
    </div>

    <x-alerts />

    <div class="SeccionSuperiorLobby">
        <div class="AccionNuevaPartida">
            <form action="{{ route('jugador.partidas.crear') }}" method="POST">
                @csrf
                <button type="submit" class="BtnNuevaPartida">NUEVA PARTIDA</button>
            </form>

            <div class="CajaDisponibles">
                <h3>Partidas disponibles para unirse</h3>
                @forelse ($disponibles as $disp)
                    <div class="ItemDisponible">
                        <span>Partida #{{ $disp->id }} — Creada por {{ $disp->jugador1->name ?? 'Jugador' }}</span>
                        <form action="{{ route('jugador.partidas.unirse', $disp->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="BtnPrimario">Unirse</button>
                        </form>
                    </div>
                @empty
                    <p class="TextoSecundario">No hay partidas en espera por ahora.</p>
                @endforelse
            </div>
        </div>


    <div class="SeccionHistorial">
        <h2>Historial de partidas</h2>

        <form action="{{ route('jugador.lobby') }}" method="GET" class="FiltrosForm">
            <div class="CampoGrupo">
                <label for="filtro_status">Resultado</label>
                <select id="filtro_status" name="status">
                    <option value="">Todos</option>
                    <option value="ganada" @selected(request('status') === 'ganada')>Ganada</option>
                    <option value="perdida" @selected(request('status') === 'perdida')>Perdida</option>
                    <option value="empate" @selected(request('status') === 'empate')>Empate</option>
                </select>
            </div>

            <div class="CampoGrupo">
                <label for="filtro_partida_id">ID de partida</label>
                <input type="number" id="filtro_partida_id" name="partida_id" value="{{ request('partida_id') }}" placeholder="Ej. 1">
            </div>

            <div class="FiltrosAcciones">
                <button type="submit" class="BtnPrimario">Filtrar</button>
                <a href="{{ route('jugador.lobby') }}" class="BtnSecundario">Limpiar</a>
            </div>
        </form>

        <div class="ContainerTabla">
            <table class="Tabla1">
                <thead>
                    <tr>
                        <th>ID PARTIDA</th>
                        <th>JUGADOR 1 (X)</th>
                        <th>JUGADOR 2 (O)</th>
                        <th>RESULTADO</th>
                        <th>FECHA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($historial as $registro)
                        <tr>
                            <td>{{ $registro->partida_id }}</td>
                            <td>{{ $registro->partida->jugador1->name ?? '---' }}</td>
                            <td>{{ $registro->partida->jugador2->name ?? '---' }}</td>
                            <td>{{ $registro->status }}</td>
                            <td>{{ $registro->created_at ? $registro->created_at->format('Y-m-d H:i') : '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No hay registros en el historial con ese filtro.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function refrescar(accion, tiempo) {
        setInterval(accion, tiempo);
    }

    function recargarPagina() {
        window.location.reload();
    }

    refrescar(recargarPagina, 5000);
</script>
</body>
</html>