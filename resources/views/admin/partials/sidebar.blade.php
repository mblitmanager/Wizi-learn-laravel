@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $isFormateur = in_array($user->role, ['formateur', 'formatrice']);

    // Menu par section : ['label', 'icône', 'route'] ou ['label', 'icône', 'children' => [[label, route], …]]
    $sections = [
        'Général' => [
            [
                'label' => 'Tableau de bord',
                'icon' => 'bx bx-home-circle',
                'children' => array_filter([
                    ['Tableau de bord', 'dashboard'],
                    $isAdmin ? ['Activité des utilisateurs', 'dashboard.activity'] : null,
                    $isAdmin ? ['Usages mobiles', 'admin.user_app_usages.index'] : null,
                    $isAdmin ? ['Stats stagiaires', 'admin.stagiaires.stats'] : null,
                    $isAdmin ? ['Inactivité', 'admin.inactivity.index'] : null,
                    $isAdmin ? ['Gestion des succès', 'admin.achievements.index'] : null,
                    $isFormateur ? ['Stats de mes stagiaires', 'formateur.stagiaires.stats'] : null,
                ]),
            ],
        ],
    ];

    if ($isFormateur) {
        $sections['Mon espace'] = [
            [
                'label' => 'Mes stagiaires',
                'icon' => 'bx bx-group',
                'children' => [
                    ['Tous mes stagiaires', 'formateur.stagiaires.index'],
                    ['En cours de formation', 'formateur.stagiaires.en-cours'],
                    ['Formation terminée', 'formateur.stagiaires.termines'],
                ],
            ],
            [
                'label' => 'Mes formations',
                'icon' => 'bx bx-library',
                'children' => [
                    ['Mes formations', 'formateur.formations.index'],
                    ['Catalogue formations', 'formateur.catalogue.index'],
                ],
            ],
            ['label' => 'Mon agenda', 'icon' => 'bx bx-calendar', 'route' => 'agenda.index'],
            ['label' => 'Mon profil', 'icon' => 'bx bx-user', 'route' => 'formateur.profile'],
        ];
    }

    if ($isAdmin) {
        $sections['Pédagogie'] = [
            ['label' => 'Stagiaires', 'icon' => 'bx bx-group', 'route' => 'stagiaires.index'],
            [
                'label' => 'Formation',
                'icon' => 'bx bx-library',
                'children' => [
                    ['Catalogue formation', 'catalogue_formation.index'],
                    ['Domaine formation', 'formations.index'],
                ],
            ],
            ['label' => 'Quiz', 'icon' => 'bx bx-brain', 'route' => 'quiz.index'],
            ['label' => 'Classement', 'icon' => 'bx bx-list-ol', 'route' => 'classement.index'],
            ['label' => 'Média', 'icon' => 'bx bx-play-circle', 'route' => 'medias.index'],
            ['label' => 'Agenda', 'icon' => 'bx bx-calendar', 'route' => 'agenda.index'],
        ];
        $sections['Relations'] = [
            [
                'label' => 'Contacts',
                'icon' => 'bx bx-phone-outgoing',
                'children' => [
                    ['Partenaires', 'partenaires.index'],
                    ['Formateurs', 'formateur.index'],
                    ['Pôle relation client', 'pole_relation_clients.index'],
                    ['Commerciaux', 'commercials.index'],
                ],
            ],
            [
                'label' => 'Parrainage',
                'icon' => 'bx bx-git-branch',
                'children' => [
                    ['Liste des parrains', 'parrainage.index'],
                    ['Événements parrainage', 'parrainage_events.index'],
                ],
            ],
            ['label' => 'Historique des demandes', 'icon' => 'bx bx-folder', 'route' => 'demande.historique.index'],
        ];
        $sections['Administration'] = [
            ['label' => 'Statistiques', 'icon' => 'bx bx-line-chart', 'route' => 'admin.parametre.reset-data'],
            [
                'label' => 'Paramètres',
                'icon' => 'bx bx-cog',
                'children' => [
                    ['Paramètres généraux', 'parametre.index'],
                    ['Rôles', 'roles.index'],
                    ['Permissions', 'permissions.index'],
                ],
            ],
        ];
    }

    // Une entrée est active sur sa route et sur les pages en dessous (…/create, …/{id}/edit)
    $current = rtrim(request()->url(), '/') . '/';
    $isActive = fn (string $route) => request()->routeIs($route)
        || ($route !== 'dashboard' && str_starts_with($current, rtrim(route($route), '/') . '/'));
@endphp

<aside class="sidebar-wrapper" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('assets/images/logowizi.png') }}" alt="Wizi Learn" class="sidebar-brand-logo">
    </a>

    <nav class="flex-grow-1 overflow-y-auto" aria-label="Menu principal">
        @foreach ($sections as $title => $items)
            <div class="sidebar-menu-section">
                <div class="sidebar-menu-title">{{ $title }}</div>
                <ul class="sidebar-menu-list">
                    @foreach ($items as $item)
                        @if (isset($item['children']))
                            @php
                                $open = collect($item['children'])->contains(fn ($child) => $isActive($child[1]));
                                $id = 'menu-' . \Illuminate\Support\Str::slug($item['label']);
                            @endphp
                            <li class="sidebar-menu-item">
                                <button type="button" class="sidebar-menu-link {{ $open ? 'parent-active' : 'collapsed' }}"
                                    data-bs-toggle="collapse" data-bs-target="#{{ $id }}"
                                    aria-expanded="{{ $open ? 'true' : 'false' }}" aria-controls="{{ $id }}"
                                    title="{{ $item['label'] }}">
                                    <i class="{{ $item['icon'] }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                    <i class="bi bi-chevron-down dropdown-caret"></i>
                                </button>
                                <ul class="sidebar-submenu collapse {{ $open ? 'show' : '' }}" id="{{ $id }}">
                                    @foreach ($item['children'] as [$label, $route])
                                        <li>
                                            <a href="{{ route($route) }}"
                                                class="sidebar-submenu-link {{ $isActive($route) ? 'active' : '' }}">{{ $label }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="sidebar-menu-item">
                                <a href="{{ route($item['route']) }}"
                                    class="sidebar-menu-link {{ $isActive($item['route']) ? 'active' : '' }}"
                                    title="{{ $item['label'] }}">
                                    <i class="{{ $item['icon'] }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <div class="sidebar-profile">
        <img src="{{ $user->image ? asset($user->image) : asset('assets/spark/avatar.png') }}" alt="{{ $user->name }}"
            class="sidebar-profile-img">
        <div class="sidebar-profile-info">
            <div class="sidebar-profile-name">{{ $user->name }}</div>
            <div class="sidebar-profile-email sidebar-profile-role">{{ $user->role }}</div>
        </div>
    </div>
</aside>
