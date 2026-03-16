<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Unirse a sala – GeoTurismo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sala/sala.css') }}">
</head>
<body class="sala-body">

    <div class="sala-entry-card">
        <div class="sala-brand">
            <div class="sala-brand-icon">🗺️</div>
            <h1>GeoTurismo</h1>
            <p>Introduce el código de sala que te ha proporcionado tu monitor para empezar la gimcana</p>
        </div>

        @if(session('error'))
            <div class="sala-alert sala-alert--error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('sala.entrar') }}" class="sala-form" autocomplete="off">
            @csrf
            <div class="form-group">
                <label for="codigo_sala">Código de sala</label>
                <input
                    id="codigo_sala"
                    name="codigo_sala"
                    type="text"
                    maxlength="8"
                    placeholder="XXXXXXXX"
                    autocomplete="off"
                    autofocus
                    value="{{ old('codigo_sala') }}"
                    class="input-sala {{ $errors->has('codigo_sala') ? 'input-error' : '' }}"
                    style="text-transform:uppercase;letter-spacing:0.15em"
                >
                @error('codigo_sala')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn-primary">
                Entrar a la sala
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="sala-logout-form">
            @csrf
            <button type="submit" class="btn-ghost-small">Cerrar sesión</button>
        </form>
    </div>

</body>
</html>
