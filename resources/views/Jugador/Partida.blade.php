<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablero Partida #{{ $partida->id }}</title>
    @vite(['resources/css/app.css', 'resources/css/componentes.css'])
</head>
<body>
    <x-navbar />

    <div class="TituloPagina">
        <h1>Tablero — Partida #{{ $partida->id }}</h1>
        <div style="margin-left: 70%">
            <a href="{{ route('jugador.lobby') }}" class="BtnSecundario">Volver a la sala</a>
        </div>
    </div>

    <x-alerts />

    <x-tablero
        :partida="$partida"
        :simboloUsuario="$simboloUsuario"
        :esMiTurno="$esMiTurno"
    />
</body>
</html>