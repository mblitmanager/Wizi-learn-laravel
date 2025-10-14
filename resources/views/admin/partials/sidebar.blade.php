<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('assets/images/logowizi.png') }}" class="logo-icon" alt="logo icon">
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i></div>
    </div>
    
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <!-- Tableau de bord -->
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-home-circle'></i></div>
                <div class="menu-title">Tableau de bord</div>
            </a>
            <ul>
                <li><a href="{{ route('dashboard') }}"><i class="bx bx-right-arrow-alt"></i>Tableau de bord</a></li>

                @if (auth()->user()->role === 'administrateur')
                    <li><a href="{{ route('dashboard.activity') }}"><i class="bx bx-right-arrow-alt"></i>Activité des utilisateurs</a></li>
                    <li><a href="{{ route('admin.user_app_usages.index') }}"><i class="bx bx-right-arrow-alt"></i>Usages mobiles</a></li>
                    <li><a href="{{ route('admin.stagiaires.stats') }}"><i class="bx bx-right-arrow-alt"></i>Stats stagiaires</a></li>
                    <li><a href="{{ route('admin.inactivity.index') }}"><i class="bx bx-right-arrow-alt"></i>Inactivité</a></li>
                    <li><a href="{{ route('admin.achievements.index') }}"><i class="bx bx-right-arrow-alt"></i>Gestion des Succès</a></li>
                @elseif(auth()->user()->role === 'Formateur')
                    <li><a href="{{ route('formateur.stagiaires.stats') }}"><i class="bx bx-right-arrow-alt"></i>Stats de mes stagiaires</a></li>
                @endif
            </ul>
        </li>

        <!-- Stagiaires -->
        @if (auth()->user()->role === 'formateur')
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='lni lni-users'></i></div>
                    <div class="menu-title">Mes Stagiaires</div>
                </a>
                <ul>
                    <li><a href="{{ route('formateur.stagiaires.index') }}"><i class="bx bx-right-arrow-alt"></i>Tous mes stagiaires</a></li>
                    <li><a href="{{ route('formateur.stagiaires.en-cours') }}"><i class="bx bx-right-arrow-alt"></i>En cours de formation</a></li>
                    <li><a href="{{ route('formateur.stagiaires.termines') }}"><i class="bx bx-right-arrow-alt"></i>Formation terminée</a></li>
                </ul>
            </li>
        @elseif(auth()->user()->role === 'administrateur')
            <li>
                <a href="{{ route('stagiaires.index') }}">
                    <div class="parent-icon"><i class='lni lni-users'></i></div>
                    <div class="menu-title">Stagiaire</div>
                </a>
            </li>
        @endif

        <!-- Quiz (Admin seulement) -->
        @if (auth()->user()->role === 'administrateur')
            <li>
                <a href="{{ route('quiz.index') }}">
                    <div class="parent-icon"><i class='fadeIn animated bx bx-brain'></i></div>
                    <div class="menu-title">Quiz</div>
                </a>
            </li>
        @endif

        <!-- Contact (Admin seulement) -->
        @if (auth()->user()->role === 'administrateur')
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='fadeIn animated bx bx-phone-outgoing'></i></div>
                    <div class="menu-title">Contact</div>
                </a>
                <ul>
                    <li><a href="{{ route('partenaires.index') }}"><i class="bx bx-right-arrow-alt"></i>Partenaire</a></li>
                    <li><a href="{{ route('formateur.index') }}"><i class="bx bx-right-arrow-alt"></i>Formateur</a></li>
                    <li><a href="{{ route('pole_relation_clients.index') }}"><i class="bx bx-right-arrow-alt"></i>Pôle relation client</a></li>
                    <li><a href="{{ route('commercials.index') }}"><i class="bx bx-right-arrow-alt"></i>Commercial</a></li>
                </ul>
            </li>
        @endif

        <!-- Classement (Admin seulement) -->
        @if (auth()->user()->role === 'administrateur')
            <li>
                <a href="{{ route('classement.index') }}">
                    <div class="parent-icon"><i class='fadeIn animated bx bx-list-ol'></i></div>
                    <div class="menu-title">Classement</div>
                </a>
            </li>
        @endif

        <!-- Formation -->
        @if (auth()->user()->role === 'formateur')
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="lni lni-library"></i></div>
                    <div class="menu-title">Mes Formations</div>
                </a>
                <ul>
                    <li><a href="{{ route('formateur.formations.index') }}"><i class="bx bx-right-arrow-alt"></i>Mes formations</a></li>
                    <li><a href="{{ route('formateur.catalogue.index') }}"><i class="bx bx-right-arrow-alt"></i>Catalogue formations</a></li>
                </ul>
            </li>
        @elseif(auth()->user()->role === 'administrateur')
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="lni lni-library"></i></div>
                    <div class="menu-title">Formation</div>
                </a>
                <ul>
                    <li><a href="{{ route('catalogue_formation.index') }}"><i class="bx bx-right-arrow-alt"></i>Catalogue formation</a></li>
                    <li><a href="{{ route('formations.index') }}"><i class="bx bx-right-arrow-alt"></i>Domaine formation</a></li>
                </ul>
            </li>
        @endif

        <!-- Profil Formateur -->
        @if (auth()->user()->role === 'formateur')
            <li>
                <a href="{{ route('formateur.profile') }}">
                    <div class="parent-icon"><i class='bx bx-user'></i></div>
                    <div class="menu-title">Mon Profil</div>
                </a>
            </li>
        @endif

        <!-- Menus Admin seulement -->
        @if (auth()->user()->role === 'administrateur')
            <!-- Parrainage -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='fadeIn animated bx bx-git-branch'></i></div>
                    <div class="menu-title">Parrainage</div>
                </a>
                <ul>
                    <li><a href="{{ route('parrainage.index') }}"><i class="bx bx-right-arrow-alt"></i>Liste des parrains</a></li>
                    <li><a href="{{ route('parrainage_events.index') }}"><i class="bx bx-right-arrow-alt"></i>Événement parrainage</a></li>
                </ul>
            </li>

            <!-- Media -->
            <li>
                <a href="{{ route('medias.index') }}">
                    <div class="parent-icon"><i class='fadeIn animated bx bx-play-circle'></i></div>
                    <div class="menu-title">Media</div>
                </a>
            </li>

            <!-- Défis -->
            <li>
                <a href="javascript:;">
                    <div class="parent-icon"><i class='fadeIn animated bx bx-target-lock'></i></div>
                    <div class="menu-title">Défis</div>
                </a>
            </li>

            <!-- Planning -->
            <li>
                <a href="javascript:;">
                    <div class="parent-icon"><i class='fadeIn animated bx bx-calendar'></i></div>
                    <div class="menu-title">Planning</div>
                </a>
            </li>

            <!-- Statistiques -->
            <li>
                <a href="{{ route('admin.parametre.reset-data') }}">
                    <div class="parent-icon"><i class='fadeIn animated bx bx-line-chart'></i></div>
                    <div class="menu-title">Statistiques</div>
                </a>
            </li>

            <!-- Historique des demandes -->
            <li>
                <a href="{{ route('demande.historique.index') }}">
                    <div class="parent-icon"><i class='bx bx-folder'></i></div>
                    <div class="menu-title">Historique des demandes</div>
                </a>
            </li>

            <!-- Paramètres -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-cog bx-spin'></i></div>
                    <div class="menu-title">Paramètres</div>
                </a>
                <ul>
                    <li><a href="{{ route('parametre.index') }}"><i class="bx bx-right-arrow-alt"></i>Paramètres généraux</a></li>
                    <li><a href="{{ route('roles.index') }}"><i class="bx bx-right-arrow-alt"></i>Rôles</a></li>
                    <li><a href="{{ route('permissions.index') }}"><i class="bx bx-right-arrow-alt"></i>Permissions</a></li>
                </ul>
            </li>
        @endif
    </ul>
    <!--end navigation-->
</div>
