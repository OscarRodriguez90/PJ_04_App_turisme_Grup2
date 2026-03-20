    {{-- ── Header ── --}}
    <header class="sala-header">
        <div class="sala-header-info">
            <span class="sala-code-badge">{{ $sala->nombre }}</span>
            <span class="sala-estado sala-estado--{{ $sala->estado }}">{{ ucfirst($sala->estado) }}</span>
        </div>
        <div class="sala-header-user">
            <span class="user-name-header"><i class="bi bi-person-circle"></i> {{ auth()->user()->nombre }}</span>
            <a href="{{ route('sala.index') }}" class="btn-ghost-small" id="btn-leave-room">
                <i class="bi bi-box-arrow-left"></i> Salir
            </a>
        </div>
    </header>

    {{-- ── Main ── --}}
    <main class="sala-main">

        @if($miEquipo)
        {{-- User already has a group --}}
        <section class="sala-section">
            <h2 class="section-heading">
                <i class="bi bi-people-fill"></i> Mi equipo
            </h2>
            <div class="equipo-card equipo-card--mine">
                <div class="equipo-card-header">
                    <span class="equipo-nombre">{{ $miEquipo->nombre_equipo }}</span>
                    <span class="equipo-count">
                        <i class="bi bi-person"></i>
                        {{ $miEquipo->integrantes->count() }}
                    </span>
                </div>
                <div class="equipo-miembros">
                    @foreach($miEquipo->integrantes as $integrante)
                        <div class="miembro-chip">
                            <img
                                src="{{ !empty($integrante->foto) ? asset('img/usuarios/' . $integrante->foto) : asset('img/usuarios/default_user.png') }}"
                                alt="Foto de {{ $integrante->nombre }}"
                                class="miembro-avatar miembro-avatar--foto"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{{ asset('img/usuarios/default_user.png') }}';"
                            >
                            <span>{{ $integrante->nombre }}</span>
                            @if($integrante->id === $miEquipo->id_lider)
                                <i class="bi bi-star-fill lider-icon" title="Líder"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button class="btn-danger-small" onclick="salirEquipo()">
                    <i class="bi bi-door-open"></i> Salir del equipo
                </button>
                @if($retosCompletados)
                    <form method="POST" action="{{ route('gimcana.reiniciar') }}" class="restart-retos-form">
                        @csrf
                        <button type="submit" class="btn-outline-small btn-start-retos">
                            <i class="bi bi-arrow-counterclockwise"></i> Volver a empezar retos
                        </button>
                    </form>
                @endif
            </div>
        </section>
        @else
        {{-- No group yet --}}
        <section class="sala-section">
            <h2 class="section-heading">
                <i class="bi bi-plus-circle"></i> ¿Listo para jugar?
            </h2>
            <p class="section-help">Crea tu propio equipo o únete a uno ya creado.</p>
            <button class="btn-primary btn-crear-equipo" onclick="abrirModalCrear()">
                <i class="bi bi-shield-plus"></i> Crear equipo
            </button>
        </section>
        @endif

        {{-- ── All groups in this sala ── --}}
        <section class="sala-section">
            <h2 class="section-heading">
                <i class="bi bi-grid"></i> Equipos en esta sala
                <span class="sala-count-badge">{{ $equipos->count() }}</span>
            </h2>

            @forelse($equipos as $equipo)
                @php
                    $esMio = $miEquipo && $miEquipo->id === $equipo->id;
                    $equipoLleno = (int) $equipo->integrantes_count >= 8;
                @endphp
                <div class="equipo-card {{ $esMio ? 'equipo-card--mine' : '' }}">
                    <div class="equipo-card-header">
                        <span class="equipo-nombre">{{ $equipo->nombre_equipo }}</span>
                        <span class="equipo-count">
                            <i class="bi bi-person"></i> {{ $equipo->integrantes_count }}/8
                        </span>
                    </div>
                    <p class="equipo-lider">
                        <i class="bi bi-star"></i>
                        Líder: {{ $equipo->lider->nombre ?? '–' }}
                    </p>
                    @if(!$miEquipo)
                        <button class="btn-outline-small" onclick="unirseEquipo({{ $equipo->id }})" {{ $equipoLleno ? 'disabled' : '' }}>
                            <i class="bi {{ $equipoLleno ? 'bi-lock-fill' : 'bi-person-plus' }}"></i>
                            {{ $equipoLleno ? 'Grupo lleno' : 'Unirse' }}
                        </button>
                    @elseif($esMio)
                        <span class="badge-mine"><i class="bi bi-check-circle-fill"></i> Tu equipo</span>
                    @endif
                </div>
            @empty
                <div class="equipos-empty">
                    <i class="bi bi-people"></i>
                    <p>Aún no hay equipos. ¡Sé el primero en crear uno!</p>
                </div>
            @endforelse
        </section>

    </main>
