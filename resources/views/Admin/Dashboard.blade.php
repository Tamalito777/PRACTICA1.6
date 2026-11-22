<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Tic-Tac-Toe</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <x-navbar />

    <div class="TituloPagina">
        <h1>Panel de Administración</h1>
    </div>

    <x-alerts />

    <div class="SeccionAdmin">
        <h2>Gestión de Usuarios</h2>

        <form action="{{ route('admin.usuarios.store') }}" method="POST" class="FiltrosForm">
            @csrf
            <div class="CampoGrupo">
                <label for="nuevo_name">Nombre</label>
                <input type="text" id="nuevo_name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="CampoGrupo">
                <label for="nuevo_email">Correo</label>
                <input type="email" id="nuevo_email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="CampoGrupo">
                <label for="nuevo_password">Contraseña</label>
                <input type="password" id="nuevo_password" name="password" required>
            </div>

            <div class="CampoGrupo">
                <label for="nuevo_rol">Rol</label>
                <select id="nuevo_rol" name="rol" required>
                    <option value="jugador">jugador</option>
                    <option value="administrador">administrador</option>
                </select>
            </div>

            <div class="FiltrosAcciones">
                <button type="submit" class="BtnPrimario">Crear Usuario</button>
            </div>
        </form>

        <div class="ContainerTabla">
            <table class="Tabla1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>CORREO</th>
                        <th>ROL</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->rol }}</td>
                            <td class="AccionesCelda">
    <input type="checkbox" id="modal-editar-{{ $usuario->id }}" class="ModalToggle">
    <label for="modal-editar-{{ $usuario->id }}" class="BtnPrimario">Actualizar</label>

    <div class="ModalFondo">
        <div class="ModalCaja">
            <div class="ModalEncabezado">
                <span>Actualizar usuario</span>
                <label for="modal-editar-{{ $usuario->id }}" class="ModalCerrar" title="Cerrar">&times;</label>
            </div>

            <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="ModalFormulario">
                @csrf
                @method('PUT')

                <div class="CampoGrupo">
                    <label for="edit_name_{{ $usuario->id }}">Nombre</label>
                    <input type="text" id="edit_name_{{ $usuario->id }}" name="name" value="{{ $usuario->name }}" required>
                </div>

                <div class="CampoGrupo">
                    <label for="edit_email_{{ $usuario->id }}">Correo</label>
                    <input type="email" id="edit_email_{{ $usuario->id }}" name="email" value="{{ $usuario->email }}" required>
                </div>

                <div class="CampoGrupo">
    <label for="edit_password_{{ $usuario->id }}">Contraseña</label>
    <input type="password" id="edit_password_{{ $usuario->id }}" name="password" placeholder="Dejar en blanco para no cambiar">
</div>

                <div class="CampoGrupo">
                    <label for="edit_rol_{{ $usuario->id }}">Rol</label>
                    <select id="edit_rol_{{ $usuario->id }}" name="rol" required>
                        <option value="jugador" @selected($usuario->rol === 'jugador')>jugador</option>
                        <option value="administrador" @selected($usuario->rol === 'administrador')>administrador</option>
                    </select>
                </div>

                <div class="ModalAcciones">
                    <button type="submit" class="BtnPrimario">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="BtnEliminar">Eliminar</button>
    </form>
</td>

                                
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="SeccionAdmin">
        <h2>Historial General de Partidas</h2>

        <form action="{{ route('admin.dashboard') }}" method="GET" class="FiltrosForm">
            <div class="CampoGrupo">
                <label for="admin_filtro_status">Resultado</label>
                <select id="admin_filtro_status" name="status">
                    <option value="">Todos</option>
                    <option value="ganada" @selected(request('status') === 'ganada')>Ganada</option>
                    <option value="perdida" @selected(request('status') === 'perdida')>Perdida</option>
                    <option value="empate" @selected(request('status') === 'empate')>Empate</option>
                </select>
            </div>

            <div class="CampoGrupo">
                <label for="admin_filtro_usuario">Jugador</label>
                <input type="text" id="admin_filtro_usuario" name="usuario" value="{{ request('usuario') }}" placeholder="Nombre del jugador">
            </div>

            <div class="FiltrosAcciones">
                <button type="submit" class="BtnPrimario">Filtrar</button>
                <a href="{{ route('admin.dashboard') }}" class="BtnSecundario">Limpiar</a>
            </div>
        </form>

        <div class="ContainerTabla">
            <table class="Tabla1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ID PARTIDA</th>
                        <th>USUARIO</th>
                        <th>RESULTADO</th>
                        <th>FECHA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($historial as $registro)
                        <tr>
                            <td>{{ $registro->id }}</td>
                            <td>{{ $registro->partida_id }}</td>
                            <td>{{ $registro->user->name ?? '---' }}</td>
                            <td>{{ $registro->status }}</td>
                            <td>{{ $registro->created_at ? $registro->created_at->format('Y-m-d H:i') : '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No hay registros en el historial general.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>