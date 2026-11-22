@props(['partida', 'simboloUsuario', 'esMiTurno'])

<div class="TableroPanel" id="panel-partida-{{ $partida->id }}" data-estado-url="{{ route('partidas.estado', $partida->id) }}">
    <div class="TableroInfo">
        <h2>Partida #{{ $partida->id }}</h2>
        <p><strong>Jugador 1 (X):</strong> {{ $partida->jugador1->name ?? '---' }}</p>
        <p><strong>Jugador 2 (O):</strong> {{ $partida->jugador2->name ?? 'Esperando rival...' }}</p>
        <p><strong>Estado:</strong> <span id="partida-status">{{ $partida->status }}</span></p>
        <p><strong>Turno actual:</strong> <span id="partida-turno">{{ $partida->turnoActual->name ?? '---' }}</span></p>
        @if ($simboloUsuario)
            <p><strong>Tu símbolo:</strong> {{ $simboloUsuario }}</p>
        @endif
        @if ($partida->ganador)
            <p id="partida-ganador"><strong>Ganador:</strong> {{ $partida->ganador->name }}</p>
        @else
            <p id="partida-ganador"></p>
        @endif
    </div>

    <div class="TableroGrid" id="tablero-grid">
        @foreach ($partida->tablero->casillasFormateadas() as $casilla)
            @if ($esMiTurno && $casilla['valor'] === null && $partida->status === 'en_juego')
                <form action="{{ route('jugador.partidas.mover', $partida->id) }}" method="POST" class="CasillaForm">
                    @csrf
                    <input type="hidden" name="casilla" value="{{ $casilla['numero'] }}">
                    <button type="submit" class="CasillaBtn CasillaLibre" data-casilla="{{ $casilla['numero'] }}">
                        &nbsp;
                    </button>
                </form>
            @else
                <div class="CasillaBtn CasillaOcupada" data-casilla="{{ $casilla['numero'] }}">
                    {{ $casilla['valor'] ?? '' }}
                </div>
            @endif
        @endforeach
    </div>

    @if ($partida->status !== 'finalizado' && $simboloUsuario)
        <form action="{{ route('jugador.partidas.abandonar', $partida->id) }}" method="POST" class="TableroAbandonar">
            @csrf
            <button type="submit" class="BtnEliminar">Abandonar partida</button>
        </form>
    @endif
</div>

<script>
    function refrescar(accion, tiempo) {
        setInterval(accion, tiempo);
    }

    function recargarPagina() {
        window.location.reload();
    }

    refrescar(recargarPagina, 3000);
</script>