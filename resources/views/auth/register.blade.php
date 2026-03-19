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
    <div class="auth-layout">
        <!-- Lado Visual (Desktop) -->
        <div class="auth-visual">
            <div class="visual-content">
                <img src="{{ asset('img/admin/logo.png') }}" alt="GeoTurismo" class="visual-logo">
                <h2>Únete a la aventura</h2>
                <p>Crea tu cuenta gratis y empieza a descubrir los mejores lugares turísticos estructurados para ti.</p>
            </div>
        </div>

        <!-- Lado Formulario -->
        <div class="auth-form-container">
            <a href="{{ url('/') }}" class="back-home">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
    <!-- Brand -->
    <a href="{{ route('home') }}" class="brand">
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
              enctype="multipart/form-data"
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
                Volver al INICIO
            </a>

            <!-- Brand visible solo en móvil -->
            <a href="{{ route('home') }}" class="brand mobile-brand">
                <img src="{{ asset('img/admin/logo.png') }}" alt="GeoTurismo">
                GeoTurismo
            </a>
            <!-- Avatar upload -->
            <div class="avatar-upload">
                <input type="file"
                       id="foto"
                       name="foto"
                       accept="image/png,image/jpeg,image/webp"
                       class="avatar-file-input">

                <div class="avatar-preview" id="avatar-preview" aria-live="polite">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>

                <button type="button" class="avatar-upload-btn" id="avatar-upload-btn">
                    Subir foto de perfil (opcional)
                </button>
            </div>
            <span class="avatar-error" id="avatar-error"></span>

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
                                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Ana" class="{{ $errors->has('nombre') ? 'input-error' : '' }}">
                            </div>
                            @if ($errors->has('nombre'))
                                <span class="field-error">{{ $errors->first('nombre') }}</span>
                            @endif
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
                                <input type="text" id="apellido1" name="apellido1" value="{{ old('apellido1') }}" placeholder="García" class="{{ $errors->has('apellido1') ? 'input-error' : '' }}">
                            </div>
                            @if ($errors->has('apellido1'))
                                <span class="field-error">{{ $errors->first('apellido1') }}</span>
                            @endif
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
                            <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="ana_viajera" autocomplete="username" class="{{ $errors->has('username') ? 'input-error' : '' }}">
                        </div>
                        @if ($errors->has('username'))
                            <span class="field-error">{{ $errors->first('username') }}</span>
                        @endif
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
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="tu@correo.com" autocomplete="email" class="{{ $errors->has('email') ? 'input-error' : '' }}">
                        </div>
                        @if ($errors->has('email'))
                            <span class="field-error">{{ $errors->first('email') }}</span>
                        @endif
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
                            <input type="password" id="password" name="password" placeholder="Mín. 8 caracteres" autocomplete="new-password" class="{{ $errors->has('password') ? 'input-error' : '' }}">
                        </div>
                        @if ($errors->has('password'))
                            <span class="field-error">{{ $errors->first('password') }}</span>
                        @endif
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
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repite la contraseña" autocomplete="new-password" class="{{ $errors->has('password_confirmation') ? 'input-error' : '' }}">
                        </div>
                        @if ($errors->has('password_confirmation'))
                            <span class="field-error">{{ $errors->first('password_confirmation') }}</span>
                        @endif
                    </div>

                    <button type="submit" class="btn-primary">Crear cuenta</button>
                </form>

                <p class="card-footer">
                    ¿Ya tienes cuenta? <a href="{{ url('/login') }}">Inicia sesión</a>
                </p>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/auth/register.js') }}"></script>
</body>
</html>
