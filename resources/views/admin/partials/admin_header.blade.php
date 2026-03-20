<header class="admin-header">
    <div class="header-left">
        <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menú">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <div class="header-title-block">
            <h1>{{ $title ?? 'GeoTurismo Admin' }}</h1>
            <p>{{ $subtitle ?? 'Panel de Control' }}</p>
        </div>
    </div>

    <div class="header-right">
        <div class="user-profile-mini">
            <div class="user-info-text">
                <span class="user-name">{{ $user->nombre }}</span>
                <span class="user-role">Admin</span>
            </div>
            @php
                $avatarUrl = $user->foto 
                    ? asset('img/usuarios/' . $user->foto) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($user->nombre) . '&background=0ea5a4&color=fff';
            @endphp
            <img src="{{ $avatarUrl }}" alt="Avatar" class="header-avatar">
        </div>
    </div>
</header>
