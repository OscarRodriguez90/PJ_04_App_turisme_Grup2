<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Crear Cuenta - GeoTurismo</title>
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
</head>

<body>

    <!-- Brand -->
    <a href="#" class="brand">
        <img src="{{ asset('img/admin/logo.png') }}" alt="GeoTurismo" class="brand-logo">
        GeoTurismo
    </a>

    <!-- Card -->
    <div class="card">

        <h1 class="card-title">Crea tu cuenta</h1>
        <p class="card-subtitle">Únete y descubre los mejores lugares turísticos</p>

        <form id="register-form"
              action="{{ route('register') }}"
              method="POST"
              data-check-username="{{ route('check-username') }}"
              data-check-email="{{ route('check-email') }}">
            @csrf

            @if($errors->any())
            <div class="error-card">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Avatar upload (visual only) -->
            <div class="avatar-upload">
                <div class="avatar-preview">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <div class="avatar-upload-btn">
                    Subir foto de perfil (opcional)
                </div>
            </div>

            <!-- Nombre y apellidos -->
            <div class="form-row-cols">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Ana">
                    </div>
                </div>

                <div class="form-group">
                    <label for="apellido1">Apellido</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input type="text" id="apellido1" name="apellido1" value="{{ old('apellido1') }}" placeholder="García">
                    </div>
                </div>
            </div>

            <!-- Nombre de usuario -->
            <div class="form-group">
                <label for="username">Nombre de usuario</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                            <line x1="18" y1="8" x2="23" y2="13"/>
                        </svg>
                    </span>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="ana_viajera" autocomplete="username">
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="tu@correo.com" autocomplete="email">
                </div>
            </div>

            <!-- Contraseña -->
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="Mín. 8 caracteres" autocomplete="new-password">
                </div>
            </div>

            <!-- Confirmar contraseña -->
            <div class="form-group">
                <label for="password_confirmation">Confirmar contraseña</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </span>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repite la contraseña" autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn-primary">Crear cuenta</button>
        </form>

        <p class="card-footer">
            ¿Ya tienes cuenta? <a href="{{ url('/login') }}">Inicia sesión</a>
        </p>
    </div>

    <script src="{{ asset('js/auth/register.js') }}"></script>
</body>
</html>
