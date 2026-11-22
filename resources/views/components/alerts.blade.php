@if (session('mensaje'))
    <div class="Alerta AlertaExito">
        {{ session('mensaje') }}
    </div>
@endif

@if (session('error'))
    <div class="Alerta AlertaError">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="Alerta AlertaError">
        <strong>Revisa los siguientes errores:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif