<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - GeoTurismo Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/mi_perfil.css') }}">
</head>
<body>
    <div class="admin-layout">
        @include('admin.admin_sidebar')

        <main class="main-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>Configuración de Perfil</h1>
                    <p>Actualiza tu información personal y foto de perfil.</p>
                </div>
            </header>

            <div class="profile-container">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-left: 1.25rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="profile-card">
                    <form action="{{ route('admin.perfil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="profile-header">
                            <div class="avatar-upload">
                                @php
                                    $avatarUrl = $user->foto 
                                        ? asset('img/usuarios/' . $user->foto) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->nombre) . '&background=0ea5a4&color=fff';
                                @endphp
                                <img src="{{ $avatarUrl }}" alt="Avatar" class="avatar-preview" id="previewImg">
                                <label for="fotoInput" class="avatar-edit-btn">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                        <circle cx="12" cy="13" r="4"></circle>
                                    </svg>
                                </label>
                                <input type="file" id="fotoInput" name="foto" style="display: none;" accept="image/*" onchange="previewFile()">
                            </div>
                            <div class="user-meta">
                                <h2 style="margin: 0; color: #0f172a;">{{ $user->nombre }} {{ $user->apellido1 }}</h2>
                                <p style="margin: 0.25rem 0 0 0; color: #64748b;">{{ $user->username }} • {{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="username">Nombre de Usuario</label>
                                <input type="text" id="username" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre', $user->nombre) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="apellido1">Primer Apellido</label>
                                <input type="text" id="apellido1" name="apellido1" class="form-control" value="{{ old('apellido1', $user->apellido1) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="apellido2">Segundo Apellido</label>
                                <input type="text" id="apellido2" name="apellido2" class="form-control" value="{{ old('apellido2', $user->apellido2) }}">
                            </div>

                            <div class="form-group">
                                <label for="password">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                                <input type="password" id="password" name="password" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                            </div>

                            <div class="form-group full-width" style="text-align: right;">
                                <button type="submit" class="btn-save">Guardar Cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function previewFile() {
            const preview = document.getElementById('previewImg');
            const file = document.getElementById('fotoInput').files[0];
            const reader = new FileReader();

            reader.onloadend = function () {
                preview.src = reader.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
