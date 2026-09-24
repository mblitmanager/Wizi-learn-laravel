@php
    $user = auth()->user();
    $avatar = $user->image
        ? asset($user->image)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=FCB829&color=231E15&size=128&bold=true';
@endphp

<header class="navbar-custom">
    <div class="navbar-left">
        <button class="btn-desktop-toggle d-none d-xl-flex align-items-center justify-content-center me-3"
            id="desktop-sidebar-toggle" type="button" aria-label="Réduire le menu">
            <i class="bi bi-chevron-bar-left"></i>
        </button>
        <button class="sidebar-toggle-btn me-2" id="sidebar-toggle" type="button" aria-label="Ouvrir le menu">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="navbar-page-title">@yield('title', 'Wizi Learn')</h1>
    </div>

    <div class="navbar-actions">
        <button class="navbar-action-btn me-1" id="btn-fullscreen" type="button" aria-label="Plein écran">
            <i class="bi bi-arrows-fullscreen"></i>
        </button>

        <div class="dropdown ms-2">
            <button class="navbar-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false" id="profile-dropdown">
                <img src="{{ $avatar }}" alt="{{ $user->name }}" class="navbar-profile-img">
                <span class="d-none d-md-inline text-start">
                    <span class="navbar-profile-name">{{ $user->name }}</span>
                    <span class="navbar-profile-role">{{ $user->role }}</span>
                </span>
                <i class="bi bi-chevron-down navbar-profile-caret"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="profile-dropdown">
                <li class="dropdown-header">Bienvenue !</li>
                <li><a class="dropdown-item" href="{{ route('parametre.show', $user->id) }}"><i class="bi bi-person"></i> Profil</a></li>
                <li><a class="dropdown-item" href="{{ route('parametre.index') }}"><i class="bi bi-gear"></i> Paramètres</a></li>
                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-grid"></i> Tableau de bord</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
            </ul>
        </div>
    </div>
</header>
