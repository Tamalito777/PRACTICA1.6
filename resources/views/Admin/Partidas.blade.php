<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todas las Partidas - Administrador</title>
    @vite(['resources/css/app.css', 'resources/css/componentes.css'])
</head>
<body>
    <x-navbar />

    <div class="TituloPagina">
        <h1>Todas las Partidas</h1>
    </div>

    <x-alerts />

    <div class="SeccionAdmin">
        <form action="{{ route('admin.partidas.index') }}" method="GET" class="FiltrosForm">
            <div class="CampoGrupo">
                <label for="partidas_filtro_status">Estado</label>
                <select id="partidas_filtro_status" name="status">
                    <option value="">Todos</option>
                    <option value="esperando" @selected(request('status') === 'esperando')>Esperando</option>
                    <option value="en_juego" @selected(request('status') === 'en_juego')>En juego</option>
                    <option value="finalizado" @selected(request('status') === 'finalizado')>Finalizado</option>
                </select>
            </div>

            <div class="CampoGrupo">
                <label for="partidas_filtro_jugador">Jugador</label>
                <input type="text" id="partidas_filtro_jugador" name="jugador" value="{{ request('jugador') }}" placeholder="Nombre del jugador">
            </div>

            <div class="FiltrosAcciones">
                <button type="submit" class="BtnPrimario">Filtrar</button>
                <a href="{{ route('admin.partidas.index') }}" class="BtnSecundario">Limpiar</a>
            </div>
        </form>

        <div class="ContainerTabla">
            <table class="Tabla1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>JUGADOR 1 (X)</th>
                        <th>JUGADOR 2 (O)</th>
                        <th>TURNO ACTUAL</th>
                        <th>GANADOR</th>
                        <th>ESTADO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($partidas as $partida)
                        <tr>
                            <td>{{ $partida->id }}</td>
                            <td>{{ $partida->jugador1->name ?? '---' }}</td>
                            <td>{{ $partida->jugador2->name ?? '---' }}</td>
                            <td>{{ $partida->turnoActual->name ?? '---' }}</td>
                            <td>{{ $partida->ganador->name ?? '---' }}</td>
                            <td>{{ $partida->status }}</td>
                            <td>
                                @if ($partida->status !== 'finalizado')
                                    <form action="{{ route('admin.partidas.cancelar', $partida->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="BtnEliminar">Cancelar partida</button>
                                    </form>
                                @else
                                    <span>Finalizada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No hay partidas registradas con ese filtro.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>