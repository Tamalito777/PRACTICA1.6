<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Tic-Tac-Toe</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="LoginCard">
        <div class="TituloPagina">
            <img src="{{ asset('img/PusheenNieve-removebg-preview.png') }}" alt="logo" style="width:60px; height:60px;">
            <h1>Iniciar Sesión</h1>
            <img src="{{ asset('img/PusheenNieve-removebg-preview.png') }}" alt="logo" style="width:60px; height:60px;">
        </div>

        <x-alerts />

        <form action="{{ route('iniciarsesion') }}" method="POST" class="FormVertical">
            @csrf

            <div class="CampoGrupo">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="CampoGrupo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="BtnPrimario">Ingresar</button>
        </form>
    </div>
</body>
</html>