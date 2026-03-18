<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Iniciar Sesión - GeoTurismo</title>
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>

<body>
    <div class="auth-layout">
        <!-- Lado Visual (Desktop) -->
        <div class="auth-visual">
            <div class="visual-content">
                <img src="{{ asset('img/admin/logo.png') }}" alt="GeoTurismo" class="visual-logo">
                <h2>Descubre, Explora, Vive</h2>
                <p>Accede a la mejor plataforma de turismo interactivo y encuentra experiencias únicas cerca de ti.</p>
            </div>
        </div>

        <!-- Lado Formulario -->
        <div class="auth-form-container">
            <a href="{{ url('/') }}" class="back-home">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Volver al INICIO
            </a>

            <!-- Brand visible solo en móvil -->
            <a href="{{ route('home') }}" class="brand mobile-brand">
                <img src="{{ asset('img/admin/logo.png') }}" alt="GeoTurismo">
                GeoTurismo
            </a>

            <div class="card">
                <h1 class="card-title">Bienvenido de nuevo</h1>
                <p class="card-subtitle">Inicia sesión para continuar explorando</p>

                @if(session('success'))
                    <div class="success-card">
                        {{ session('success') }}
                    </div>
                @endif

                <form id="login-form" action="{{ route('login') }}" method="POST">
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
                            <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" class="{{ $errors->has('password') ? 'input-error' : '' }}">
                        </div>
                        @if ($errors->has('password'))
                            <span class="field-error">{{ $errors->first('password') }}</span>
                        @endif
                    </div>

                    <div class="form-row">
                        <label class="remember">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            Recordarme
                        </label>
                        
                    </div>

                    <button type="submit" class="btn-primary">Iniciar sesión</button>
                </form>

                <p class="card-footer">
                    ¿No tienes cuenta? <a href="{{ url('/register') }}">Regístrate</a>
                </p>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/auth/login.js') }}"></script>
</body>
</html>
